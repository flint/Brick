<?php

namespace Brick\Provider;

use Pimple\Container;
use Puli\Extension\Twig\PuliTemplateLoader;

class PuliServiceProvider implements \Pimple\ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Container $app)
    {
        $app['puli.factory'] = function ($app) {
            $factoryClass = PULI_FACTORY_CLASS;

            return new $factoryClass();
        };

        $app['puli.repository'] = function ($app) {
            return $app['puli.factory']->createRepository();
        };

        $app['puli.discovery'] = function ($app) {
            return $app['puli.factory']->createDiscovery($app['puli.repository']);
        };

        if (isset($app['twig.loader'])) {
            $app->extend('twig.loader', function ($loader, $app) {
                $loader->addLoader(new PuliTemplateLoader($app['puli.repository']));

                return $loader;

            });
        }
    }
}
