<?php

declare(strict_types=1);

namespace Mezzio\DebugBar;

use DebugBar\DebugBar as Bar;
use DebugBar\JavascriptRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function file_exists;
use function file_get_contents;
use function in_array;
use function ob_get_clean;
use function ob_start;
use function pathinfo;
use function session_status;
use function stripos;
use function strlen;
use function strpos;
use function strripos;
use function strtolower;
use function substr;

use const PATHINFO_EXTENSION;
use const PHP_SESSION_ACTIVE;

class DebugBarMiddleware implements MiddlewareInterface
{
    public const DISABLE_KEY = 'X-Disable-Debug-Bar';

    private static array $mimes = [
        'css'   => 'text/css',
        'js'    => 'text/javascript',
        'otf'   => 'font/opentype',
        'eot'   => 'application/vnd.ms-fontobject',
        'svg'   => 'image/svg+xml',
        'ttf'   => 'application/font-sfnt',
        'woff'  => 'application/font-woff',
        'woff2' => 'application/font-woff2',
    ];

    private Bar $debugBar;
    private bool $captureAjax = false;
    private bool $inline      = false;
    private ResponseFactoryInterface $responseFactory;
    private StreamFactoryInterface $streamFactory;
    private array $debugBarConfig;
    private JavascriptRenderer $renderer;

    /**
     * Set the debug bar.
     */
    public function __construct(
        Bar $debugBar,
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        array $debugBarConfig
    ) {
        $this->debugBar        = $debugBar;
        $this->responseFactory = $responseFactory;
        $this->streamFactory   = $streamFactory;
        $this->debugBarConfig  = $debugBarConfig;
        $this->renderer        = $this->debugBar->getJavascriptRenderer();
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->setConfigOptions();
        //Asset response
        if ($assetResponse = $this->getAssetResponse($request)) {
            return $assetResponse;
        }

        $response = $handler->handle($request);

        if ($this->disableDebugBar($request, $response)) {
            return $response;
        }

        $isAjax = $this->isAjax($request);

        //Redirection response
        if ($this->isRedirection($response)) {
            return $this->handleRedirect($response);
        }

        //Html response
        if ($this->isHtml($response)) {
            return $this->handleHtml($response, $isAjax);
        }

        //Ajax response
        if ($isAjax && $this->captureAjax) {
            return $this->handleAjaxWithNonHtmlResponse($response);
        }

        return $response;
    }

    private function setConfigOptions(): void
    {
        $renderOptions = $this->debugBarConfig[ 'javascript_renderer' ] ?? [];
        // Configure whether capture ajax requests to send the data with headers.
        $this->captureAjax = $this->debugBarConfig[ 'captureAjax' ] ?? false;
        // Configure whether the js/css code should be inserted inline in the html.
        $this->inline = $this->debugBarConfig[ 'inline' ] ?? false;

        if ($renderOptions) {
            $this->renderer->setOptions($renderOptions);
        }
        if ($renderOptions['bind_ajax_handler_to_fetch'] ?? false) {
            $this->renderer->setBindAjaxHandlerToFetch();
        }
        if ($renderOptions['bind_ajax_handler_to_xhr'] ?? false) {
            $this->renderer->setBindAjaxHandlerToXHR();
        }
    }

    private function disableDebugBar(ServerRequestInterface $request, ResponseInterface $response): bool
    {
        $disableByConfig       = $this->debugBarConfig['disable'] ?? false;
        $disableHeaderValue    = $request->getHeaderLine(self::DISABLE_KEY) ?? false;
        $disableCookieValue    = $request->getCookieParams()[self::DISABLE_KEY] ?? false;
        $disableAttributeValue = $request->getAttribute(self::DISABLE_KEY, '') ?? false;
        $isDownload            = strpos($response->getHeaderLine('Content-Disposition'), 'attachment;') !== false;

        if ($disableByConfig || $isDownload || $disableHeaderValue || $disableCookieValue || $disableAttributeValue) {
            return true;
        }

        return false;
    }

    private function getAssetResponse(ServerRequestInterface $request): ?ResponseInterface
    {
        $path    = $request->getUri()->getPath();
        $baseUrl = $this->renderer->getBaseUrl();

        if (strpos($path, $baseUrl) === 0) {
            $file = $this->renderer->getBasePath() . substr($path, strlen($baseUrl));

            if (file_exists($file)) {
                $response = $this->responseFactory->createResponse();
                $response->getBody()->write((string) file_get_contents($file));
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                if (isset(self::$mimes[$extension])) {
                    return $response->withHeader('Content-Type', self::$mimes[$extension]);
                }

                return $response;
            }
        }
        return null;
    }

    /**
     * Handle redirection responses
     */
    private function handleRedirect(ResponseInterface $response): ResponseInterface
    {
        if ($this->debugBar->isDataPersisted() || session_status() === PHP_SESSION_ACTIVE) {
            $this->debugBar->stackData();
        }

        return $response;
    }

    /**
     * Handle html responses
     */
    private function handleHtml(ResponseInterface $response, bool $isAjax): ResponseInterface
    {
        $html = (string) $response->getBody();
        if (! $isAjax) {
            if ($this->inline) {
                ob_start();
                echo "<style>\n";
                $this->renderer->dumpCssAssets();
                echo "\n</style>";
                echo "<script>\n";
                $this->renderer->dumpJsAssets();
                echo "\n</script>";
                $code = (string) ob_get_clean();
            } else {
                $code = $this->renderer->renderHead();
            }

            $html = self::injectHtml($html, $code, '</head>');
        }

        $html = self::injectHtml($html, $this->renderer->render(! $isAjax), '</body>');

        $body = $this->streamFactory->createStream();

        $body->write($html);

        return $response
            ->withBody($body)
            ->withoutHeader('Content-Length');
    }

    /**
     * Handle ajax responses In the case you are sending back non-HTML data (eg: JSON)
     * If you are sending a lot of data through headers, it may cause problems with your browser.
     * Instead we use a storage handler and the open handler  to load the data after an ajax request
     */
    private function handleAjaxWithNonHtmlResponse(ResponseInterface $response): ResponseInterface
    {
        if ($this->debugBar->getStorage() === null) {
            $headers = $this->debugBar->getDataAsHeaders();
        } else {
            $this->renderer->setOpenHandlerUrl('open.php');
            $this->debugBar->getData();
            $headerName = 'phpdebugbar-id';
            $headers    = [$headerName => $this->debugBar->getCurrentRequestId()];
        }
        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        return $response;
    }

    /**
     * Inject html code before a tag.
     */
    private static function injectHtml(string $html, string $code, string $before): string
    {
        $pos = strripos($html, $before);

        if ($pos === false) {
            return $html . $code;
        }

        return substr($html, 0, $pos) . $code . substr($html, $pos);
    }

    private function isHtml(ResponseInterface $response): bool
    {
        return stripos($response->getHeaderLine('Content-Type'), 'text/html') === 0;
    }

    private function isAjax(ServerRequestInterface $request): bool
    {
        return strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest';
    }

    private function isRedirection(ResponseInterface $response): bool
    {
        return in_array($response->getStatusCode(), [302, 301]);
    }
}
