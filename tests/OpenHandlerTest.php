<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests;

use DebugBar\DebugBar;
use DebugBar\DebugBarException;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Mezzio\DebugBar\OpenHandler;
use Mezzio\DebugBar\Tests\Storage\MockStorage;
use PHPUnit\Framework\TestCase;

class OpenHandlerTest extends TestCase
{
    private OpenHandler $openHandler;

    public function setUp(): void
    {
        $this->debugbar = new DebugBar();
        $this->debugbar->setStorage(new MockStorage(['foo' => ['__meta' => ['id' => 'foo']]]));
        $this->openHandler = new OpenHandler($this->debugbar);
    }

    public function testFind()
    {
        $request  = new ServerRequest(
            [],
            [],
            null,
            null,
            'php://input',
            ['Accept' => 'application/json'],
            [],
            ['op' => 'find']
        );
        $response = $this->openHandler->handle($request);
        self::assertInstanceOf(JsonResponse::class, $response);
    }

    public function testGet(): void
    {
        $request = new ServerRequest([], [], null, null, 'php://input', [], [], ['op' => 'get', 'id' => 'foo']);

        $response = $this->openHandler->handle($request);
        self::assertInstanceOf(JsonResponse::class, $response);
        $jsonResponse = new JsonResponse(['__meta' => ['id' => 'foo']]);
         self::assertEquals($response->getPayload(), $jsonResponse->getPayload());
    }

    public function testGetMissingId(): void
    {
        $this->expectException(DebugBarException::class);
        $request = new ServerRequest([], [], null, null, 'php://input', [], [], ['op' => 'get']);
        $this->openHandler->handle($request);
    }

    public function testNotAllowedOperation(): void
    {
        $this->expectException(DebugBarException::class);
        $request = new ServerRequest([], [], null, null, 'php://input', [], [], ['op' => 'bar', 'id' => 'foo']);
        $this->openHandler->handle($request);
    }

    public function testClear()
    {
        $request = new ServerRequest([], [], null, null, 'php://input', [], [], ['op' => 'clear']);

        $response = $this->openHandler->handle($request);
        self::assertInstanceOf(JsonResponse::class, $response);

        $jsonResponse = new JsonResponse(['success' => true]);
        self::assertEquals($response->getPayload(), $jsonResponse->getPayload());
    }
}
