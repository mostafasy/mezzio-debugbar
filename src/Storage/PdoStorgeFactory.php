<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Storage;

use DebugBar\Storage\PdoStorage;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PdoStorgeFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PdoStorage
    {
        $config = $container->get('config')[ 'debugbar' ][ 'pdo' ];
        $pdo    = new PDO($config[ 'dsn' ], $config[ 'user' ], $config[ 'password' ]);
        return new PdoStorage($pdo);
    }
}
