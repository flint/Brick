<?php

namespace Brick\Tests\Provider;

use Brick\Provider\DebugServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Silex\Application;

class DebugServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->app = new Application(['root_dir' => __DIR__ . '/../Fixtures', 'debug' => true]);
        $this->app->register(new TwigServiceProvider);
        $this->app->register(new WebProfilerServiceProvider);
        $this->app->register(new DebugServiceProvider);
    }

    public function testSanity()
    {
        $this->assertTrue(isset($this->app['debug.cloner']));
        $this->assertTrue(isset($this->app['debug.data_collector']));
    }

    public function testDumpListenerIsRegistered()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->once())->method('addSubscriber');

        $provider = new DebugServiceProvider;
        $provider->register($this->app);
        $provider->subscribe($this->app, $dispatcher);
    }

    public function testDataCollectorIsRegistered()
    {
        $collectors = $this->app['data_collectors'];

        // mimic the provider, so we can compare the objects.
        $this->assertEquals($this->app['debug.data_collector'], $collectors['dump']($this->app));

        $this->assertTrue(in_array(['dump', '@Debug/Profiler/dump.html.twig'], $this->app['data_collector.templates'], true));
    }

    public function testTwigExtensionIsRegistered()
    {
        // we arent really interested in what kind of function it is, just that it isnt a false
        // value, which would indicate the function is not available
        $this->assertNotFalse($this->app['twig']->getFunction('dump'));
    }

    public function testImplementsEventListenerProvider()
    {
        $this->assertInstanceOf('Silex\Api\EventListenerProviderInterface', new DebugServiceProvider);
    }
}
