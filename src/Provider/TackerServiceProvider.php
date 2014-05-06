<?php

namespace Brick\Provider;

use Pimple\Container;
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

class TackerServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['tacker.config'] = function ($pimple) {
            $options = isset($pimple['tacker.options']) ? $pimple['tacker.options'] : [];
            $paths = isset($pimple['root_dir']) ? [$pimple['root_dir']] : [];
            $debug = isset($pimple['debug']) ? $pimple['debug'] : true;

            return $options + [
                'cache_dir' => null,
                'debug'     => $debug,
                'paths'     => $paths,
            ];
        };

        $pimple['tacker.locator'] = function ($pimple) {
            return new FileLocator($pimple['tacker.config']['paths']);
        };

        $pimple['tacker.resource_collection'] = function ($pimple) {
            return new ResourceCollection;
        };

        $pimple['tacker.normalizer'] = function ($pimple) {
            return new ChainNormalizer([
                new EnvfileNormalizer($pimple['tacker.locator']),
                new PimpleNormalizer($pimple),
                new EnvironmentNormalizer,
            ]);
        };

        $pimple['tacker.loader'] = function ($pimple) {
            $loader = new NormalizerLoader(new DelegatingLoader($pimple['tacker.loader_resolver']), $pimple['tacker.normalizer']);
            $loader = new CacheLoader($loader, $pimple['tacker.resource_collection']);

            $loader->setCacheDir($pimple['tacker.config']['cache_dir']);
            $loader->setDebug($pimple['tacker.config']['debug']);

            return $loader;
        };

        $pimple['tacker.loader_resolver'] = function ($pimple) {
            return new LoaderResolver([
                new JsonFileLoader($pimple['tacker.locator'], $pimple['tacker.resource_collection']),
                new IniFileLoader($pimple['tacker.locator'], $pimple['tacker.resource_collection']),
                new PhpFileLoader($pimple['tacker.locator'], $pimple['tacker.resource_collection']),
                new YamlFileLoader($pimple['tacker.locator'], $pimple['tacker.resource_collection']),
            ]);
        };

        $pimple['tacker.configurator'] = function ($pimple) {
            return new Configurator($pimple['tacker.loader']);
        };
    }
}
