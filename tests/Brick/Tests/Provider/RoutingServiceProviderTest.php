<?php

namespace Brick\Tests\Provider;

use Brick\Provider\RoutingServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RoutingServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->app = new Application(array('root_dir' => __DIR__ . '/../Fixtures', 'debug' => true));
        $this->app->register(new RoutingServiceProvider);
    }

    public function testOptionsReachRouter()
    {
        $this->app['routing.options'] = array(
            'resource' => 'routing.xml',
            'cache_dir' => sys_get_temp_dir(),
            'debug' => false,
        );

        $router = $this->app['routing.router'];

        $this->assertFalse($router->getOption('debug'));
        $this->assertEquals(sys_get_temp_dir(), $router->getOption('cache_dir'));
    }

    public function testUrlGeneratorIsRouter()
    {
        $this->assertSame($this->app['routing.router'], $this->app['url_generator']);
    }

    public function testRoutingDefaults()
    {
        $router = $this->app['routing.router'];

        $this->assertTrue($router->getOption('debug'));
        $this->assertInternalType('null', $router->getOption('cache_dir'));

        $this->assertEquals('Silex\Provider\Routing\RedirectableUrlMatcher', $router->getOption('matcher_class'));
        $this->assertEquals('Silex\Provider\Routing\RedirectableUrlMatcher', $router->getOption('matcher_base_class'));
    }

    /**
     * @dataProvider routerProvider
     */
    public function testRouter($resource, $content)
    {
        $this->app['routing.options'] = compact('resource');

        $this->app->get('/hello', function () {
            return 'world from silex closure';
        });

        $response = $this->app->handle(Request::create('/hello'));

        $this->assertEquals($content, $response->getContent());
    }

    public function routerProvider()
    {
        return array(
            array(null, 'world from silex closure'),
            array('routing.xml', 'world from xml'),
            array('routing.php', 'world from php'),
            array('routing.yml', 'world from yml'),
        );
    }
}
