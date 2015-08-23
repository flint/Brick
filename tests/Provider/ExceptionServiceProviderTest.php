<?php

namespace Brick\Tests\Provider;

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Brick\Provider\ExceptionServiceProvider;

class ExceptionServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->app = new Application();

        $this->app->register(new TwigServiceProvider);
        $this->app->register(new ExceptionServiceProvider);
    }

    public function testOverrideExceptionHandler()
    {
        $this->assertInstanceOf('Symfony\Component\HttpKernel\EventListener\ExceptionListener', $this->app['exception_handler']);
    }

    public function testExceptionController()
    {
        $provider = new TwigServiceProvider;
        $provider->register($this->app);

        $this->assertInstanceOf('Brick\Controller\ExceptionController', $this->app['exception_controller']);
    }

}
