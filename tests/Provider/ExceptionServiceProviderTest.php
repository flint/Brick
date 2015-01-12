<?php

namespace Brick\Tests\Provider;

use Silex\Provider\TwigServiceProvider;
use Brick\Provider\ExceptionServiceProvider;
use Pimple\Container;

class ExceptionServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pimple = new Container([
            'exception_handler' => function () {
                return 'exception_handler';
            },
            'logger' => function () {},
        ]);

        $provider = new ExceptionServiceProvider;
        $provider->register($this->pimple);
    }

    public function testOverrideExceptionHandler()
    {
        $provider = new TwigServiceProvider;
        $provider->register($this->pimple);

        $this->assertInstanceOf('Symfony\Component\HttpKernel\EventListener\ExceptionListener', $this->pimple['exception_handler']);
    }

    public function testExceptionController()
    {
        $provider = new TwigServiceProvider;
        $provider->register($this->pimple);

        $this->assertInstanceOf('Brick\Controller\ExceptionController', $this->pimple['exception_controller']);
    }

}
