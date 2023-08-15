<?php
declare(strict_types=1);

namespace Mezzio\DebugBar;
use DebugBar\Bridge\DoctrineCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DebugBar;
use DebugBar\Storage\FileStorage;
use Mezzio\DebugBar\DataCollector\ConfigCollectorFactory;
use Mezzio\DebugBar\DataCollector\DoctrineCollectorFactory;
use Mezzio\DebugBar\Storage\FileStorageFactory;

class ConfigProvider
{

    /**
     * Returns the configuration array
     *
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'debugbar'=>$this->getConfig(),
            'middleware_pipeline' => $this->getMiddelewarePipeline()
        ];
    }

    public function getConfig():array{

        return [
                'disable'     => false ,
                'captureAjax' => true,
                'inline'      => false,
                'collectors'          => [
                    ConfigCollector::class,
                ],
                'javascript_renderer' => [
                    'base_url' => '/phpdebugbar',
                    'ajax_handler_bind_to_jquery' => false ,
                    'bind_ajax_handler_to_fetch'=> true,
                    'bind_ajax_handler_to_xhr'=> true,
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
                DebugBarMiddleware::class    => DebugBarMiddlewareFactory::class,
                ConfigCollector::class       => ConfigCollectorFactory::class,
                DoctrineCollector::class     => DoctrineCollectorFactory::class,
                DebugBar::class              => StandardDebugBarFactory::class,
                FileStorage::class           => FileStorageFactory::class ,
            ],
        ];
    }

    /**
     * @return array[]
     */
    protected function getMiddelewarePipeline(): array
    {
        return [
            DebugBarMiddleware::class => [
                'middleware' => [
                    DebugBarMiddleware::class,
                ],
                'priority'   => 1000,
            ],
        ];
    }

}
