<?php
declare (strict_types=1);

namespace Mezzio\DebugBar;

use DebugBar\StandardDebugBar;
use Psr\Container\ContainerInterface;

class StandardDebugBarFactory
{
    public function __invoke(ContainerInterface $container): StandardDebugBar
    {
        $debugBar = new StandardDebugBar();

        $config = $container->get('config');

        $collectors = $config['debugbar']['collectors'];

        foreach ($collectors as $collectorName) {
            $collector = $container->get($collectorName);
            $debugBar->addCollector($collector);
        }

        $storage = $config['debugbar']['storage'];

        if (is_string( $storage)) {
            $debugBar->setStorage(
                $container->get($storage)
            );
        }

        return $debugBar;
    }
}
