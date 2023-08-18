<?php

declare(strict_types=1);

namespace Mezzio\DebugBar;

use DebugBar\DebugBar;
use DebugBar\DebugBarException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

class OpenHandler implements RequestHandlerInterface
{
    protected const  ALLOW_OPERATION = ['find', 'get', 'clear'];
    protected const FILTER_KEYS      = ['utime', 'datetime', 'ip', 'uri', 'method'];

    protected DebugBar $debugBar;

    public function __construct(DebugBar $debugBar)
    {
        $this->debugBar = $debugBar;
    }

    /**
     * @throws DebugBarException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $op          = $queryParams['op'] ?? 'find';
        if (! in_array($op, self::ALLOW_OPERATION)) {
            throw new DebugBarException('Invalid operation:' . $op);
        }
        switch ($op) {
            case 'get':
                $response = $this->get($queryParams);
                break;
            case 'clear':
                $response = $this->clear();
                break;
            default:
                $response = $this->find($queryParams);
        }
        return new JsonResponse($response);
    }

    protected function find(array $queryParams): array
    {
        $max = $queryParams['max'] ?? 20;

        $offset = $queryParams['offset'] ?? 0;

        $filters = [];
        foreach (self::FILTER_KEYS as $key) {
            if (isset($queryParams[$key])) {
                $filters[$key] = $queryParams[$key];
            }
        }

        return $this->debugBar->getStorage()->find($filters, $max, $offset);
    }

    /**
     * @throws DebugBarException
     */
    protected function get(array $queryParams): array
    {
        $id = $queryParams['id'] ?? null;
        if ($id === null) {
            throw new DebugBarException("Missing 'id' parameter in 'get' operation");
        }
        return $this->debugBar->getStorage()->get($id);
    }

    protected function clear(): array
    {
        $this->debugBar->getStorage()->clear();
        return ['success' => true];
    }
}
