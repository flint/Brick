<?php

namespace Brick\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Bridge\Twig\Extension\DumpExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\DataCollector\DumpDataCollector;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class DebugServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['debug.cloner'] = function () {
            return new VarCloner;
        };

        $pimple['debug.data_collector'] = function ($app) {
            return new DumpDataCollector($app['stopwatch']);
        };

        if (class_exists('Symfony\\Component\\HttpKernel\\EventListener\\DumpListener')) {
            $pimple->extend('twig', function ($twig, $pimple) {
                $twig->addExtension(new DumpExtension($pimple['debug.cloner'])); // needs debug.cloner

                return $twig;
            });
        }

        // if the provider exists do some stuff
        if (isset($pimple['data_collectors'])) {
            $pimple['data_collector.templates'] = [['dump' => '@Debug/Profiler/dump.html.twig']] + $pimple['data_collector.templates'];

            $pimple->extend('data_collectors', function ($collectors, $app) {
                $collectors['dump'] = $app->raw('debug.data_collector');

                return $collectors;
            });
        }
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        if (isset($app['data_collectors'])) {
            $dispatcher->addSubscriber(
                new DumpListener($app['debug.cloner'], $app['debug.dump_data_collector'])
            );
        }
    }
}
