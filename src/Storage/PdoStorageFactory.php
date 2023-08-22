<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Storage;

use DebugBar\DebugBarException;
use DebugBar\Storage\PdoStorage;
use PDO;
use PDOException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PdoStorageFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function __invoke(ContainerInterface $container): PdoStorage
    {
        $config = $container->get('config')[ 'debugbar' ][ 'pdo' ] ?? null;
        if ($config === null) {
            throw new DebugBarException("missing config for Pdo");
        }
        try {
            $pdo = new PDO($config[ 'dsn' ], $config[ 'user' ], $config[ 'password' ]);
        } catch (PDOException $exception) {
            throw new DebugBarException('pdo exception:' . $exception->getMessage());
        }
        return new PdoStorage($pdo);
    }
}
