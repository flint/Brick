<?php

namespace Brick\Provider;

use Pimple\Container;
use Tacker\Configurator;
use Tacker\LoaderBuilder;
use Tacker\Normalizer\PimpleNormalizer;

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

        $pimple['tacker.builder'] = function ($pimple) {
            $config = $pimple['tacker.config'];

            return LoaderBuilder::create($config['paths'], $config['cache_dir'], $config['debug'])
                ->addDefaultNormalizers()
                ->configureNormalizers(function ($chain) use ($pimple) {
                    $chain->add(new PimpleNormalizer($pimple));
                })
            ;
        };

        $pimple['tacker.configurator'] = function ($pimple) {
            return new Configurator($pimple['tacker.builder']->build());
        };
    }
}
