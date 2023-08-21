<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\Tests\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class MockCollector extends DataCollector implements Renderable
{
    protected array $data;
    protected string $name;
    protected array $widgets;
    /**
     * @inheritDoc
     */
    public function __construct($data = [], $name = 'mock', $widgets = [])
    {
        $this->data    = $data;
        $this->name    = $name;
        $this->widgets = $widgets;
    }

    /**
     * @inheritDoc
     */
    public function collect()
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getWidgets()
    {
        return $this->widgets;
    }
}
