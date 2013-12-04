<?php

namespace Brick\Provider;

use Pimple;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Tacker\Configurator;
use Tacker\Loader\CacheLoader;
use Tacker\Loader\IniFileLoader;
use Tacker\Loader\JsonFileLoader;
use Tacker\Loader\NormalizerLoader;
use Tacker\Loader\PhpFileLoader;
use Tacker\Loader\YamlFileLoader;
use Tacker\Normalizer\ChainNormalizer;
use Tacker\Normalizer\EnvironmentNormalizer;
use Tacker\Normalizer\EnvfileNormalizer;
use Tacker\Normalizer\PimpleNormalizer;
use Tacker\ResourceCollection;

class TackerServiceProvider implements \Silex\Api\ServiceProviderInterface
{
    public function register(Pimple $app)
    {
        $app['tacker.config'] = function ($app) {
            $options = isset($app['tacker.options']) ? $app['tacker.options'] : array();
            $paths = isset($app['root_dir']) ? array($app['root_dir']) : array();
            $debug = isset($app['debug']) ? $app['debug'] : true;

            return $options + array(
                'cache_dir' => null,
                'debug'     => $debug,
                'paths'     => $paths,
            );
        };

        $app['tacker.locator'] = function ($app) {
            return new FileLocator($app['tacker.config']['paths']);
        };

        $app['tacker.resource_collection'] = function ($app) {
            return new ResourceCollection;
        };

        $app['tacker.normalizer'] = function ($app) {
            return new ChainNormalizer(array(
                new EnvfileNormalizer($app['tacker.locator']),
                new PimpleNormalizer($app),
                new EnvironmentNormalizer,
            ));
        };

        $app['tacker.loader'] = function ($app) {
            $loader = new NormalizerLoader(new DelegatingLoader($app['tacker.loader_resolver']), $app['tacker.normalizer']);
            $loader = new CacheLoader($loader, $app['tacker.resource_collection']);

            $loader->setCacheDir($app['tacker.config']['cache_dir']);
            $loader->setDebug($app['tacker.config']['debug']);

            return $loader;
        };

        $app['tacker.loader_resolver'] = function ($app) {
            return new LoaderResolver(array(
                new JsonFileLoader($app['tacker.locator'], $app['tacker.resource_collection']),
                new IniFileLoader($app['tacker.locator'], $app['tacker.resource_collection']),
                new PhpFileLoader($app['tacker.locator'], $app['tacker.resource_collection']),
                new YamlFileLoader($app['tacker.locator'], $app['tacker.resource_collection']),
            ));
        };

        $app['tacker.configurator'] = function ($app) {
            return new Configurator($app['tacker.loader']);
        };
    }
}
