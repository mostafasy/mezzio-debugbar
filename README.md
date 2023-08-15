
-forked from : https://github.com/middlewares/debugbar

-forked from : https://github.com/php-middleware/phpdebugbar

# mezzio/debugbar

[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to insert [PHP DebugBar](http://phpdebugbar.com) automatically in html responses for Mezzio Framwork.

## Requirements

* PHP >= 7.4

## Installation

Tbd

## Example

This package supplies a config provider, which could be added to your config/config.php when using laminas-config-aggregator or mezzio-config-manager. However, because it should only be enabled in development, we recommend creating a "local" configuration file (e.g., config/autoload/php-debugbar.local.php) when you need to enable it, with the following contents:

```php
use DebugBar\Bridge\DoctrineCollector;
use DebugBar\Storage\FileStorage;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\Stdlib\ArrayUtils;

$aggregator = new ConfigAggregator(
    [
        Mezzio\DebugBar\ConfigProvider::class,
    ]
);
return ArrayUtils::merge(
    $aggregator->getMergedConfig(),
    [
// here you can overload the default Values .as example add doctrine collector or fileStorge
        'debugbar'     => [
            'disable'    => false,
            'collectors' => [
                DoctrineCollector::class,
            ],
            'storage'    => FileStorage::class,
            'storge_dir' =>'path/to-your-storge-dir'
        ],
        'dependencies' => [
            'factories' => [
                DoctrineCollector::class => \Mezzio\DebugBar\DataCollector\DoctrineCollectorFactory::class,
            ],
        ],
    ]
);
```

### inline

Set true to dump the js/css code inline in the html. This fixes (or mitigate) some issues related with loading the debugbar assets.

### renderOptions

Use this option to pass  render options to the debugbar as an array. A list of available options can be found at https://github.com/maximebf/php-debugbar/blob/master/src/DebugBar/JavascriptRenderer.php#L132

An example usage would be to pass a new location for the ``base_url`` so that you can rewrite the location of the files needed to render the debug bar. This can be used with symlinks, .htaccess or routes to the files to ensure the debugbar files are accessible.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/debugbar.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/debugbar/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/debugbar.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/debugbar
[link-downloads]: https://packagist.org/packages/middlewares/debugbar
