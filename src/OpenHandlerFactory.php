<?php

declare(strict_types=1);

namespace Mezzio\DebugBar;

use DebugBar\DebugBar;
use DebugBar\DebugBarException;
use Psr\Container\ContainerInterface;

class OpenHandlerFactory
{
    /**
     * @throws DebugBarException
     */
    public function __invoke(ContainerInterface $container): OpenHandler
    {
        $debugBar = $container->get(DebugBar::class);
        if ($debugBar->getStorage() === null) {
            throw new DebugBarException("DebugBar must have a storage to use OpenHandler");
        }
        return new OpenHandler($debugBar);
    }
}
