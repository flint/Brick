<?php

namespace Brick\Provider;

use Brick\ControllerResolver;
use Brick\Routing\ChainMatcher;
use Brick\Routing\NullLoader;
use Brick\Routing\AnnotationClassLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Pimple;
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

class RoutingServiceProvider implements \Silex\Api\ServiceProviderInterface
{
    public function register(Pimple $app)
    {
        $app->extend('url_matcher', function ($matcher, $app) {
            // By overriding with a ChainRouter we get around trying to dump closures that
            // are added, as they are added on the normal RouteCollection used by Silex.
            // This also means that ->getRouteCollection() will not be called on the Router
            // and it will therefor not be forced to reload its already cached routes.
            $matcher = new ChainMatcher(array($app['routing.router'], $matcher));
            $matcher->setContext($app['request_context']);

            return $matcher;
        });

        $app->extend('resolver', function ($resolver, $app) {
            return new ControllerResolver($resolver, $app);
        });

        $app['url_generator'] = $app->factory(function ($app) {
            return $app['routing.router'];
        });

        $app['routing.config'] = function ($app) {
            $options = isset($app['routing.options']) ? $app['routing.options'] : array();
            $debug = isset($app['debug']) ? $app['debug'] : true;
            $paths = isset($app['root_dir']) ? array($app['root_dir']) : array();

            return $options + array(
                'resource' => null,
                'debug' => $debug,
                'paths' => $paths,
                'matcher_class' => 'Silex\\Provider\\Routing\\RedirectableUrlMatcher',
                'matcher_base_class' => 'Silex\\Provider\\Routing\\RedirectableUrlMatcher',
            );
        };

        $app['routing.router'] = function ($app) {
            $config = $app['routing.config'];
            $resource = $config['resource'];

            unset($config['paths'], $config['resource']);

            return new Router($app['routing.loader'], $resource, $config, $app['request_context'], $app['logger']);
        };


        $app['routing.loader_resolver'] = function ($app) {
            $locator = $app['routing.locator'];

            $resolver = new LoaderResolver(array(
                new PhpFileLoader($locator),
                new XmlFileLoader($locator),
                new YamlFileLoader($locator),
                new NullLoader,
                new ClosureLoader,
            ));

            if (class_exists('Doctrine\Common\Annotations\AnnotationReader')) {
                $loader = new AnnotationClassLoader($app['routing.annotation_reader']);

                $resolver->addLoader(new AnnotationFileLoader($locator, $loader));
                $resolver->addLoader(new AnnotationDirectoryLoader($locator, $loader));
            }

            return $resolver;
        };

        $app['routing.loader'] = function ($app) {
            return new DelegatingLoader($app['routing.loader_resolver']);
        };

        $app['routing.locator'] = function ($app) {
            return new FileLocator($app['routing.config']['paths']);
        };

        $app['routing.annotation_reader'] = function ($app) {
            return new AnnotationReader;
        };
    }
}
