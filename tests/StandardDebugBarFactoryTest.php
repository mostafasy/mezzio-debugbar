<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests;

use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DebugBar;
use DebugBar\StandardDebugBar;
use Mezzio\DebugBar\StandardDebugBarFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

class StandardDebugBarFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->collector = new ConfigCollector(['s' => 'bar', 'a' => [], 'o' => new stdClass()]);
    }

    public function testFactory(): void
    {
        $this->container
            ->method('has')
            ->will($this->returnValueMap([
                [DebugBar::class, true],
                [ConfigCollector::class, true],
            ]));

        $this->container
            ->method('get')
            ->will($this->returnValueMap([
                [DebugBar::class, new DebugBar()],
                [ConfigCollector::class, $this->collector],
                [
                    'config',
                    [
                        'debugbar' => ['collectors' => [ConfigCollector::class], 'storage' => null],
                    ],
                ],
            ]));

        $factory  = new StandardDebugBarFactory();
        $debugbar = $factory($this->container);

        self::assertInstanceOf(StandardDebugBar::class, $debugbar);
    }
}
