<?php

namespace Brick\Provider;

use Brick\Controller\ExceptionController;
use Pimple;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class ExceptionServiceProvider implements \Silex\Api\ServiceProviderInterface
{
    public function register(Pimple $app)
    {
        $app['exception_controller'] = function ($app) {
            return new ExceptionController($app['twig']);
        };

        $app->extend('exception_handler', function ($handler, $app) {
            if (isset($app['twig']) && !$app['debug']) {
                return new ExceptionListener($app['exception_controller'], $app['logger']);
            }

            return $handler;
        });
    }
}
