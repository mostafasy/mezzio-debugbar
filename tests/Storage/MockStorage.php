<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests\Storage;

use DebugBar\Storage\StorageInterface;

use function array_slice;

class MockStorage implements StorageInterface
{
    public array $data;

    /**
     * @inheritDoc
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function save($id, $data)
    {
        $this->data[$id] = $data;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->data[$id];
    }

    /**
     * @inheritDoc
     */
    public function find(array $filters = [], $max = 20, $offset = 0)
    {
        return array_slice($this->data, $offset, $max);
    }

    public function clear()
    {
        $this->data = [];
    }
}
