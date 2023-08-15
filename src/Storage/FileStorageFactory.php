<?php
declare(strict_types=1);

namespace Mezzio\DebugBar\Storage;

use DebugBar\Storage\FileStorage;
use Psr\Container\ContainerInterface;

class FileStorageFactory
{
    public function __invoke(ContainerInterface $container): FileStorage
    {
        $config = $container->get('config');
        $dirname = $config['storge_dir'] ?? 'data/debugbar' ;
        return new FileStorage($dirname);
    }
}
