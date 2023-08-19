<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests;

use DebugBar\Bridge\DoctrineCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DebugBar;
use DebugBar\Storage\FileStorage;
use DebugBar\Storage\PdoStorage;
use Mezzio\DebugBar\ConfigProvider;
use Mezzio\DebugBar\DebugBarMiddleware;
use Mezzio\DebugBar\OpenHandler;
use Mezzio\DebugBar\Storage\DoctrineStorage;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    private ConfigProvider $provider;

    public function setUp(): void
    {
        $this->provider = new ConfigProvider();
    }

    public function testProviderDefinesExpectedFactoryServices(): void
    {
        $factories = $this->provider->getDependencies()['factories'] ?? [];

        self::assertArrayHasKey(DebugBarMiddleware::class, $factories);
        self::assertArrayHasKey(ConfigCollector::class, $factories);
        self::assertArrayHasKey(DoctrineCollector::class, $factories);
        self::assertArrayHasKey(DebugBar::class, $factories);
        self::assertArrayHasKey(FileStorage::class, $factories);
        self::assertArrayHasKey(OpenHandler::class, $factories);
        self::assertArrayHasKey(DoctrineStorage::class, $factories);
        self::assertArrayHasKey(PdoStorage::class, $factories);
    }

    public function testInvocationReturnsArrayWithDependencies(): void
    {
        $config = ($this->provider)();
        self::assertIsArray($config);
        self::assertArrayHasKey('dependencies', $config);
        self::assertArrayHasKey('factories', $config['dependencies']);

        self::assertArrayHasKey('debugbar', $config);
        self::assertArrayHasKey('disable', $config['debugbar']);
        self::assertArrayHasKey('collectors', $config['debugbar']);
        self::assertArrayHasKey('captureAjax', $config['debugbar']);
        self::assertArrayHasKey('collectors', $config['debugbar']);
        self::assertArrayHasKey('storage', $config['debugbar']);
        self::assertArrayHasKey('javascript_renderer', $config['debugbar']);
        self::assertArrayHasKey('base_url', $config['debugbar']['javascript_renderer']);
        self::assertArrayHasKey('ajax_handler_bind_to_jquery', $config['debugbar']['javascript_renderer']);
        self::assertArrayHasKey('bind_ajax_handler_to_fetch', $config['debugbar']['javascript_renderer']);
        self::assertArrayHasKey('bind_ajax_handler_to_xhr', $config['debugbar']['javascript_renderer']);

        self::assertArrayHasKey('middleware_pipeline', $config);
        self::assertArrayHasKey(DebugBarMiddleware::class, $config['middleware_pipeline']);
        self::assertArrayHasKey('middleware', $config['middleware_pipeline'][DebugBarMiddleware::class]);
        self::assertArrayHasKey('priority', $config['middleware_pipeline'][DebugBarMiddleware::class]);

        self::assertArrayHasKey('routes', $config);
        self::assertArrayHasKey(ConfigProvider::OPEN_HANDLER_URL, $config['routes']);
        self::assertArrayHasKey('path', $config['routes'][ConfigProvider::OPEN_HANDLER_URL]);
        self::assertArrayHasKey('middleware', $config['routes'][ConfigProvider::OPEN_HANDLER_URL]);
        self::assertArrayHasKey('allowed_methods', $config['routes'][ConfigProvider::OPEN_HANDLER_URL]);
    }
}
