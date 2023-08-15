<?php
declare(strict_types=1);

namespace Mezzio\DebugBar\DataCollector;


use DebugBar\Bridge\DoctrineCollector;
use Doctrine\DBAL\Logging\DebugStack;
use Psr\Container\ContainerInterface;

class DoctrineCollectorFactory
{
    public function __invoke( ContainerInterface $container, $requestedName, ?array $options = null ): DoctrineCollector
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(new DebugStack());

        return new DoctrineCollector($entityManager);
    }
}
