<?php

namespace Brick\Provider;

use Brick\Controller\ExceptionController;
use Pimple\Container;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class ExceptionServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['exception_controller'] = function ($pimple) {
            return new ExceptionController($pimple['twig']);
        };

        $pimple->extend('exception_handler', function ($handler, $pimple) {
            return new ExceptionListener($pimple['exception_controller'], $pimple['logger']);
        });
    }
}
