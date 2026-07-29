<?php

declare(strict_types=1);

namespace Mezzio\DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\Resettable;
use Laminas\Diactoros\ServerRequestFactory;
use Mezzio\Router\RouterInterface;
use function is_string;

class RouteCollector extends DataCollector implements Renderable, Resettable
{
    protected string $name;

    protected array $config;

    protected array $data;
    protected RouterInterface $router;

    public function __construct(RouterInterface $router, array $config)
    {
        $this->router = $router;
        $this->config = $config;
        $this->name   = 'Route';
    }

    public function reset(): void
    {
        $this->data = [];
    }
    /**
     * @return array
     */
    public function collect(): array
    {
        $this->data = $this->getRouteInformation();
        foreach ( $this->data as $k => $v ) {
            if ($this->isHtmlVarDumperUsed()) {
                $v = $this->getDataFormatter()->formatVar( $v );
            } elseif (! is_string($v)) {
                $v = $this->getDataFormatter()->formatVar($v);
            }
            $this->data [ $k ] = $v;
        }
        return $this->data;
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
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @return array
     */
    public function getWidgets(): array
    {
        $name = $this->getName();
        $widget = match (true) {
            $this->isJsonVarDumperUsed() => "PhpDebugBar.Widgets.JsonVariableListWidget",
            $this->isHtmlVarDumperUsed() => "PhpDebugBar.Widgets.HtmlVariableListWidget",
            default => "PhpDebugBar.Widgets.VariableListWidget",
        };
        return [
            "$name" => [
                "icon"   => "adjustments",
                "widget" => $widget,
                "map"    => "$name",
                "default" => "{}",
            ],
        ];
    }

}
