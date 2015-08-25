<?php

namespace Brick\Provider;

use Pimple\Container;
use Puli\TwigExtension\PuliExtension;
use Puli\TwigExtension\PuliTemplateLoader;

class PuliServiceProvider implements \Pimple\ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['puli.factory'] = function () {
            $factoryClass = PULI_FACTORY_CLASS;

            return new $factoryClass();
        };

        $app['puli.repository'] = function ($app) {
            return $app['puli.factory']->createRepository();
        };

        $app['puli.discovery'] = function ($app) {
            return $app['puli.factory']->createDiscovery();
        };

        $app['puli.url_generator'] = function ($app) {
            return $app['puli.factory']->createUrlGenerator($app['puli.discovery']);
        };

        if (isset($app['twig'])) {
            if (interface_exists('Puli\UrlGenerator\Api\UrlGenerator')) {
                $app->extend('twig', function ($twig, $app) {
                    $twig->addExtension(new PuliExtension($app['puli.repository'], $app['puli.url_generator']));

                    return $twig;
                });
            }

            $app->extend('twig.loader', function ($loader, $app) {
                $loader->addLoader(new PuliTemplateLoader($app['puli.repository']));

                return $loader;
            });
        }
    }
}
