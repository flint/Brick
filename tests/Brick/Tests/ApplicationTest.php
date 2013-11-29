<?php

namespace Brick\Tests;

use Brick\Application;
use Symfony\Component\HttpFoundation\Request;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->app = new Application(array(
            'root_dir' => __DIR__ . '/Fixtures',
            'debug' => true,
        ));;
    }

    public function testServicesExists()
    {
        $keys = $this->app->keys();

        $this->assertContains('routing.router', $keys);
        $this->assertContains('tacker.configurator', $keys);
    }

    public function testLoadConfig()
    {
        $this->app->configure('config.json');

        $this->assertEquals('world', $this->app['hello']);
    }
}
