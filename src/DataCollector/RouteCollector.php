<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\DataCollector;

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Laminas\Diactoros\ServerRequestFactory;
use Mezzio\Router\RouterInterface;

use function is_string;

class RouteCollector extends DataCollector implements Renderable, AssetProvider
{
    protected string $name;

    protected array $config;

    protected RouterInterface $router;

    // The HTML var dumper requires debug bar users to support the new inline assets, which not all
    // may support yet - so return false by default for now.
    protected bool $useHtmlVarDumper = false;

    public function __construct(RouterInterface $router, array $config)
    {
        $this->router = $router;
        $this->config = $config;
        $this->name   = 'Route';
    }


    /**
     * @return array
     */
    public function collect(): array
    {
        $data = $this->getRouteInformation();
        foreach ($data as $k => $v) {
            if ($this->isHtmlVarDumperUsed()) {
                $v = $this->getVarDumper()->renderVar($v);
            } elseif (! is_string($v)) {
                $v = $this->getDataFormatter()->formatVar($v);
            }
            $data[$k] = $v;
        }
        return $data;
    }

    /**
     * @return string[]
     */
    protected function getRouteInformation(): array
    {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );

        $match = $this->router->match($request);

        return $this->config[ 'routes' ][ $match->getMatchedRouteName() ] ?? ['no data'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return $this->isHtmlVarDumperUsed() ? $this->getVarDumper()->getAssets() : [];
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        $name   = $this->getName();
        $widget = $this->isHtmlVarDumperUsed()
            ? "PhpDebugBar.Widgets.HtmlVariableListWidget"
            : "PhpDebugBar.Widgets.VariableListWidget";
        return [
            "$name" => [
                "icon"    => "gear",
                "widget"  => $widget,
                "map"     => "$name",
                "default" => "{}",
            ],
        ];
    }

    /**
     * Sets a flag indicating whether the Symfony HtmlDumper will be used to dump variables for
     * rich variable rendering.
     *
     * @param bool $value
     * @return $this
     */
    public function useHtmlVarDumper($value = true): RouteCollector
    {
        $this->useHtmlVarDumper = $value;
        return $this;
    }

    /**
     * Indicates whether the Symfony HtmlDumper will be used to dump variables for rich variable
     * rendering.
     *
     * @return mixed
     */
    public function isHtmlVarDumperUsed()
    {
        return $this->useHtmlVarDumper;
    }
}
