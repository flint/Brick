<?php

namespace Brick\Provider;

use Brick\ControllerResolver;
use Brick\Routing\ChainMatcher;
use Brick\Routing\ChainUrlGenerator;
use Brick\Routing\NullLoader;
use Brick\Routing\AnnotationClassLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Pimple\Container;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Loader\AnnotationFileLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Router;

class RoutingServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple->extend('request_matcher', function ($matcher, $pimple) {
            $matcher = new ChainMatcher([$pimple['routing.router'], $matcher]);
            $matcher->setContext($pimple['request_context']);

            return $matcher;
        });

        $pimple->extend('url_generator', function ($generator, $pimple) {
            $generator = new ChainUrlGenerator([$pimple['routing.router'], $generator]);
            $generator->setContext($pimple['request_context']);

            return $generator;
        });

        $pimple->extend('resolver', function ($resolver, $pimple) {
            return new ControllerResolver($resolver, $pimple);
        });

        $pimple['routing.config'] = function ($pimple) {
            $options = isset($pimple['routing.options']) ? $pimple['routing.options'] : [];
            $debug = isset($pimple['debug']) ? $pimple['debug'] : true;
            $paths = isset($pimple['root_dir']) ? [$pimple['root_dir']] : [];

            return $options + [
                'resource'           => null,
                'debug'              => $debug,
                'paths'              => $paths,
                'matcher_class'      => 'Silex\\Provider\\Routing\\RedirectableUrlMatcher',
                'matcher_base_class' => 'Silex\\Provider\\Routing\\RedirectableUrlMatcher',
            ];
        };

        $pimple['routing.router'] = function ($pimple) {
            $config = $pimple['routing.config'];
            $resource = $config['resource'];

            unset($config['paths'], $config['resource']);

            return new Router($pimple['routing.loader'], $resource, $config, $pimple['request_context'], $pimple['logger']);
        };

        $pimple['routing.loader_resolver'] = function ($pimple) {
            $locator = $pimple['routing.locator'];

            $resolver = new LoaderResolver([
                new PhpFileLoader($locator),
                new XmlFileLoader($locator),
                new YamlFileLoader($locator),
                new NullLoader,
                new ClosureLoader,
            ]);

            if (class_exists('Doctrine\Common\Annotations\AnnotationReader')) {
                $loader = new AnnotationClassLoader($pimple['routing.annotation_reader']);

                $resolver->addLoader(new AnnotationFileLoader($locator, $loader));
                $resolver->addLoader(new AnnotationDirectoryLoader($locator, $loader));
            }

            return $resolver;
        };

        $pimple['routing.loader'] = function ($pimple) {
            return new DelegatingLoader($pimple['routing.loader_resolver']);
        };

        $pimple['routing.locator'] = function ($pimple) {
            return new FileLocator($pimple['routing.config']['paths']);
        };

        $pimple['routing.annotation_reader'] = function ($pimple) {
            return new AnnotationReader;
        };
    }
}
