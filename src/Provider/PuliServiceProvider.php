<?php

namespace Brick\Provider;

use Pimple\Container;

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
    }
}
