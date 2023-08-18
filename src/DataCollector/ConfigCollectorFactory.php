<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\DataCollector;

use DebugBar\DataCollector\ConfigCollector;
use Psr\Container\ContainerInterface;

class ConfigCollectorFactory
{
    public function __invoke(ContainerInterface $container): ConfigCollector
    {
        $data = $container->get('config');

        return new ConfigCollector($data);
    }
}
