<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Storage;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DoctrineStorageFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): DoctrineStorage
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        $config = $container->get('config')['debugbar' ]['doctrine_storge'] ?? [];

        return new DoctrineStorage($entityManager, $config);
    }
}
