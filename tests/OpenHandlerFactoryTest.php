<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests;

use DebugBar\DebugBar;
use DebugBar\DebugBarException;
use DebugBar\StandardDebugBar;
use Mezzio\DebugBar\OpenHandler;
use Mezzio\DebugBar\OpenHandlerFactory;
use Mezzio\DebugBar\Tests\Storage\MockStorage;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class OpenHandlerFactoryTest extends TestCase
{
    public DebugBar $debugbar;

    public MockStorage $storage;

    public function setUp(): void
    {
        $this->debugbar = $this->createMock(DebugBar::class);

        $this->storage = new MockStorage(['storge' => ['__meta' => ['id' => 'Xstorge']]]);
    }

    public function testFactoryWillThrowExeceptionIfDebugbarStorogeIsNull(): void
    {
        $this->expectException(DebugBarException::class);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->will($this->returnValueMap([
                [DebugBar::class, true],
            ]));
        $container
            ->method('get')
            ->will($this->returnValueMap([
                [DebugBar::class, $this->debugbar],
            ]));

        $factory     = new OpenHandlerFactory();
        $openHandler = $factory($container);
    }

    public function testFactory(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $debugBar  = (new StandardDebugBar())->setStorage($this->storage);
        $container
            ->method('has')
            ->will($this->returnValueMap([
                [DebugBar::class, true],
            ]));

        $container
            ->method('get')
            ->will($this->returnValueMap([
                [DebugBar::class, $debugBar],
            ]));

        $factory     = new OpenHandlerFactory();
        $openHandler = $factory($container);

        self::assertInstanceOf(OpenHandler::class, $openHandler);
    }
}
