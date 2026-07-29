<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\DataCollector;

use DebugBar\Bridge\Doctrine\DoctrineCollector;
use Psr\Container\ContainerInterface;

class DoctrineCollectorFactory
{
    public function __invoke(ContainerInterface $container): DoctrineCollector
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');
        $debugBarSQLMiddleware = new \DebugBar\Bridge\Doctrine\DebugBarSQLMiddleware();
        return new DoctrineCollector($debugBarSQLMiddleware);
    }
}
