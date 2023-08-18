<?php

declare(strict_types=1);

namespace Mezzio\DebugBar;

use DebugBar\DebugBar;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class DebugBarMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): DebugBarMiddleware
    {
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory   = $container->get(StreamFactoryInterface::class);
        $debugBar        = $container->get(DebugBar::class);
        $config          = $container->get('config');
        $debugbarConfig  = $config['debugbar'] ?? [];

        return new DebugBarMiddleware($debugBar, $responseFactory, $streamFactory, $debugbarConfig);
    }
}
