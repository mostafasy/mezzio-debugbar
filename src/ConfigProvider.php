<?php

declare(strict_types=1);

namespace Mezzio\DebugBar;

use DebugBar\Bridge\DoctrineCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DebugBar;
use DebugBar\Storage\FileStorage;
use DebugBar\Storage\PdoStorage;
use Mezzio\DebugBar\DataCollector\ConfigCollectorFactory;
use Mezzio\DebugBar\DataCollector\DoctrineCollectorFactory;
use Mezzio\DebugBar\DataCollector\RouteCollector;
use Mezzio\DebugBar\DataCollector\RouteCollectorFactory;
use Mezzio\DebugBar\Storage\DoctrineStorage;
use Mezzio\DebugBar\Storage\DoctrineStorageFactory;
use Mezzio\DebugBar\Storage\FileStorageFactory;
use Mezzio\DebugBar\Storage\PdoStorageFactory;

final class ConfigProvider
{
    public const OPEN_HANDLER_URL = 'debugbarOpen';

    /**
     * Returns the configuration array
     */
    public function __invoke(): array
    {
        return [
            'dependencies'        => $this->getDependencies(),
            'debugbar'            => $this->getConfig(),
            'middleware_pipeline' => $this->getMiddelewarePipeline(),
            'routes'              => $this->getRoutes(),
        ];
    }

    public function getConfig(): array
    {
        return [
            'disable'             => false,
            'captureAjax'         => false,
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
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                DebugBarMiddleware::class => DebugBarMiddlewareFactory::class,
                ConfigCollector::class    => ConfigCollectorFactory::class,
                DoctrineCollector::class  => DoctrineCollectorFactory::class,
                DebugBar::class           => StandardDebugBarFactory::class,
                FileStorage::class        => FileStorageFactory::class,
                OpenHandler::class        => OpenHandlerFactory::class,
                DoctrineStorage::class    => DoctrineStorageFactory::class,
                PdoStorage::class         => PdoStorageFactory::class,
                RouteCollector::class     => RouteCollectorFactory::class,
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function getMiddelewarePipeline(): array
    {
        return [
            DebugBarMiddleware::class => [
                'middleware' => [
                    DebugBarMiddleware::class,
                ],
                'priority'   => 20000,
            ],
        ];
    }

    public function getRoutes(): array
    {
        return [
            self::OPEN_HANDLER_URL => [
                'path'            => '/' . self::OPEN_HANDLER_URL,
                'middleware'      => OpenHandler::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
        ];
    }
}
