<?php

namespace Brick\Tests;

use Brick\Application;
use Symfony\Component\HttpFoundation\Request;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->rootDir = __DIR__ . '/Fixtures';

        $this->app = new Application($this->rootDir);
    }

    public function testDefaultsAreSet()
    {
        $this->assertEquals(__DIR__ . '/Fixtures', $this->app['root_dir']);
        $this->assertTrue($this->app['debug']);
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
