<?php

namespace Brick\Tests\Provider;

use Silex\Provider\TwigServiceProvider;
use Brick\Provider\ExceptionServiceProvider;
use Pimple;

class ExceptionServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pimple = new Pimple(array(
            'exception_handler' => function () { return 'exception_handler'; },
            'logger' => function () {},
        ));

        $provider = new ExceptionServiceProvider;
        $provider->register($this->pimple);
    }

    public function testDefaultExceptionHandlerWhenDebug()
    {
        $this->pimple['debug'] = true;

        $this->assertEquals('exception_handler', $this->pimple['exception_handler']);
    }

    public function testDefaultExceptionHandlerWhenDebugOffAndTwigMissing()
    {
        $this->pimple['debug'] = false;

        $this->assertEquals('exception_handler', $this->pimple['exception_handler']);
    }

    public function testOverrideExceptionHandler()
    {
        $this->pimple['debug'] = false;

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
