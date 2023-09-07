<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\DataCollector;

use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

class RouteCollectorFactory
{
    public function __invoke(ContainerInterface $container): RouteCollector
    {
        $config = $container->get('config');
        $router = $container->get(RouterInterface::class);

        return new RouteCollector($router, $config);
    }
}
