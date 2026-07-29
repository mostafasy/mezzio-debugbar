<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\DataCollector;

use DebugBar\Bridge\Doctrine\DebugBarSQLMiddleware;
use DebugBar\Bridge\Doctrine\DoctrineCollector;
use DebugBar\DebugBarException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class DoctrineCollectorFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws DebugBarException
     * @throws NotFoundExceptionInterface
     */
    public function __invoke( ContainerInterface $container): DoctrineCollector
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');
        $middlewares = $entityManager->getConnection()->getConfiguration()->getMiddlewares();
        $debugBarSQLMiddleware = null;
        foreach ($middlewares as $middleware) {
            if ($middleware instanceof DebugBarSQLMiddleware) {
                $debugBarSQLMiddleware = $middleware;
                break;
            }
        }
        if ($debugBarSQLMiddleware === null) {
            throw new RuntimeException(
                'DebugBarSQLMiddleware was not found in the Doctrine configuration. '
                . 'Please register it under "middlewares" in the Doctrine config.'
            );
        }
        return new DoctrineCollector($debugBarSQLMiddleware);
    }
}
