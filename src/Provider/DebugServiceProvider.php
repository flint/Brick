<?php

namespace Brick\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Bridge\Twig\Extension\DumpExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\DataCollector\DumpDataCollector;
use Symfony\Component\HttpKernel\EventListener\DumpListener;
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

        if (isset($pimple['twig'])) {
            if (class_exists('Symfony\\Component\\HttpKernel\\EventListener\\DumpListener')) {
                $pimple->extend('twig', function ($twig, $pimple) {
                    $twig->addExtension(new DumpExtension($pimple['debug.cloner']));

                    return $twig;
                });
            }

            $pimple->extend('twig.loader.filesystem', function ($loader, $app) {
                // The only class we could use to find the directory depends on DependencyInjection component
                // which we dont use. For that reason we try and find the vendor dir instead, and use that.
                $r = new \ReflectionClass('Silex\Application');
                $views = dirname($r->getFilename()) . '/../../../../symfony/debug-bundle/Symfony/Bundle/DebugBundle/Resources/views';

                $loader->addPath($views, 'Debug');

                return $loader;
            });
        }

        // if the provider exists do some stuff
        if (isset($pimple['data_collectors'])) {
            $pimple['data_collector.templates'] = array_merge($pimple['data_collector.templates'], [['dump', '@Debug/Profiler/dump.html.twig']]);

            $pimple->extend('data_collectors', function ($collectors) {
                $collectors['dump'] = function ($app) {
                    return $app['debug.data_collector'];
                };

                return $collectors;
            });
        }
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        if (isset($app['data_collectors'])) {
            $dispatcher->addSubscriber(
                new DumpListener($app['debug.cloner'], $app['debug.data_collector'])
            );
        }
    }
}
