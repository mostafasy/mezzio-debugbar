<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Storage;

use DebugBar\DebugBarException;
use DebugBar\Storage\FileStorage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class FileStorageFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws DebugBarException
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): FileStorage
    {
        $dirname = $container->get('config')['debugbar' ]['storage_dir'] ?? null;

        if ($dirname === null) {
                throw new DebugBarException("missing config:fileStorage needs storage_dir");
        }

        return new FileStorage($dirname);
    }
}
