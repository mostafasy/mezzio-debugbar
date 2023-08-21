<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestHandlerStub implements RequestHandlerInterface
{
    private ResponseInterface $response;

    private bool $called = false;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->called = true;

        return $this->response;
    }

    public function isCalled(): bool
    {
        return $this->called;
    }
}
