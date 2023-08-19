<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests;

use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DebugBar;
use Mezzio\DebugBar\DebugBarMiddleware;
use Mezzio\DebugBar\DebugBarMiddlewareFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use TypeError;

class DebugBarMiddlewareFactoryTest extends TestCase
{
    /** @var callable */
    private $responseFactory;
    /** @var callable */
    private $streamFactory;
    /** @var DebugBar */
    private $debugbar;

    public function setUp(): void
    {
        $this->debugbar        = $this->createMock(DebugBar::class);
        $this->responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $this->streamFactory   = $this->createMock(StreamFactoryInterface::class);
    }

    public function testInvokeWithEmptyContainer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory   = new DebugBarMiddlewareFactory();
        $this->expectException(TypeError::class);
        $factory($container);
    }

    public function testInvokeWithContainerEmptyConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->will($this->returnValueMap([
                [DebugBar::class, true],
                [ResponseInterface::class, true],
                [StreamInterface::class, true],
            ]));
        $container
            ->method('get')
            ->will($this->returnValueMap([
                [DebugBar::class, $this->debugbar],
                [ResponseInterface::class, $this->responseFactory],
                [StreamInterface::class, $this->streamFactory],
                ['config', []],
            ]));

        $factory = new DebugBarMiddlewareFactory();

        $this->expectException(TypeError::class);
        $factory($container);
    }

    public function testInvokeWithContainerAndConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->will($this->returnValueMap([
                [DebugBar::class, true],
                [ResponseInterface::class, true],
                [StreamInterface::class, true],
                [ResponseFactoryInterface::class, false],
                [StreamFactoryInterface::class, false],
            ]));
        $container
            ->method('get')
            ->will($this->returnValueMap([
                [DebugBar::class, $this->debugbar],
                [ResponseFactoryInterface::class, $this->responseFactory],
                [StreamFactoryInterface::class, $this->streamFactory],
                [
                    'config',
                    [
                        'debugbar' => ['collectors' => ConfigCollector::class, 'storage' => null],
                    ],
                ],
            ]));

        $factory            = new DebugBarMiddlewareFactory();
        $debugBarMiddelware = $factory($container);
        $this->assertInstanceOf(DebugBarMiddleware::class, $debugBarMiddelware);
    }
}
