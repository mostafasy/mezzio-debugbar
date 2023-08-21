<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests;

use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DebugBar;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\StreamFactory;
use Mezzio\DebugBar\DebugBarMiddleware;
use PHPUnit\Framework\TestCase;

class DebugBarMiddlewareTest extends TestCase
{
    protected DebugBar $debugbar;
    protected DebugBarMiddleware $middleware;

    protected function setUp(): void
    {
        $this->debugbar         = $this->createMock(DebugBar::class);
        $this->debugbarRenderer = $this->debugbar->getJavascriptRenderer();
        $this->responseFactory  = new ResponseFactory();
        $this->streamFactory    = new StreamFactory();
        $this->config           = [
            'disable'             => false,
            'captureAjax'         => true,
            'inline'              => false,
            'collectors'          => [
                ConfigCollector::class,
            ],
            'javascript_renderer' => [
                'base_url'                    => '/phpdebugbar',
                'ajax_handler_bind_to_jquery' => false,
                'bind_ajax_handler_to_fetch'  => true,
                'bind_ajax_handler_to_xhr'    => true,
            ],
            'storage'             => null,
        ];
        $this->middleware       = new DebugBarMiddleware(
            new DebugBar(),
            $this->responseFactory,
            $this->streamFactory,
            $this->config
        );
    }

    public function testConstructor(): void
    {
        $middleware = new DebugBarMiddleware(
            new DebugBar(),
            $this->responseFactory,
            $this->streamFactory,
            $this->config
        );
        self::assertInstanceOf(DebugBarMiddleware::class, $middleware);
    }

    public function testNotAttachIfNotAccept(): void
    {
        $request  = new ServerRequest();
        $response = new Response();
        $response->getBody()->write('ResponseBody');
        $requestHandler = new RequestHandlerStub($response);

        $result = $this->middleware->process($request, $requestHandler);

        $this->assertTrue($requestHandler->isCalled(), 'Request handler is not called');
        $this->assertSame('ResponseBody', (string) $result->getBody());
        $this->assertSame($response, $result);
    }

    public function testDisableDebugbarIfHeaderPresents(): void
    {
        $request  = new ServerRequest(
            [],
            [],
            null,
            null,
            'php://input',
            ['Accept' => 'application/json', 'X-Disable-Debug-Bar' => 'true']
        );
        $response = new Response();
        $response->getBody()->write('ResponseBody');
        $requestHandler = new RequestHandlerStub($response);

        $result = $this->middleware->process($request, $requestHandler);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame("ResponseBody", (string) $result->getBody());
    }

    public function testDisableDebugbarIfCookiePresents(): void
    {
        $cookies  = ['X-Disable-Debug-Bar' => 'true'];
        $request  = new ServerRequest(
            [],
            [],
            null,
            null,
            'php://input',
            ['Accept' => 'application/json'],
            $cookies
        );
        $response = new Response();
        $response->getBody()->write('ResponseBody');
        $requestHandler = new RequestHandlerStub($response);

        $result = $this->middleware->process($request, $requestHandler);

        $this->assertSame("ResponseBody", (string) $result->getBody());
    }

    public function testDisableDebugbarIfAttributePresents(): void
    {
        $request  = new ServerRequest(
            [],
            [],
            null,
            null,
            'php://input',
            ['Accept' => 'application/json']
        );
        $request  = $request->withAttribute('X-Disable-Debug-Bar', 'true');
        $response = new Response();
        $response->getBody()->write('ResponseBody');
        $requestHandler = new RequestHandlerStub($response);

        $result = $this->middleware->process($request, $requestHandler);

        $this->assertSame("ResponseBody", (string) $result->getBody());
    }
}
