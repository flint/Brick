<?php

namespace Brick\Tests\Provider;

use Brick\Provider\TackerServiceProvider;
use Pimple\Container;

class TackerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->provider = new TackerServiceProvider;
    }

    public function testDefaultConfig()
    {
        $this->provider->register($pimple = new Container);

        $this->assertEquals(['debug' => true, 'paths' => [], 'cache_dir' => null], $pimple['tacker.config']);

        $params = ['root_dir' => __DIR__, 'debug' => false, 'tacker.options' => [
            'cache_dir' => sys_get_temp_dir(),
        ]];

        $this->provider->register($pimple = new Container($params));

        $this->assertEquals(['debug' => false, 'paths' => [__DIR__], 'cache_dir' => sys_get_temp_dir()], $pimple['tacker.config']);
    }

    /**
     * @dataProvider loadConfigProvider
     */
    public function testLoadConfig($file)
    {
        $this->provider->register($pimple = new Container([
            'root_dir' => __DIR__ . '/../Fixtures',
        ]));

        $pimple['tacker.configurator']->configure($pimple, $file);

        $this->assertEquals('world', $pimple['hello']);
        $this->assertEquals($pimple['root_dir'], $pimple['pimple_normalized']);
        $this->assertEquals(getenv('BRICK_PHPUNIT'), $pimple['env_normalized']);
    }

    public function loadConfigProvider()
    {
        return [
            ['config.json'],
            ['config.php'],
            ['config.yml'],
            ['config.ini'],
        ];
    }
}
