<?php

namespace Brick\Tests\Provider;

use Pimple\Container;
use Brick\Provider\PuliServiceProvider;

class PuliServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testServices()
    {
        $app = new Container;

        $app->register(new PuliServiceProvider);

        $keys = $app->keys();

        $this->assertInstanceOf('Puli\Factory\PuliFactory', $app['puli.factory']);
        $this->assertContains('puli.repository', $keys);
        $this->assertContains('puli.discovery', $keys);
    }
}
