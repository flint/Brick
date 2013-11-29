<?php

namespace Brick\Tests\Provider;

use Pimple;
use Brick\Provider\TackerServiceProvider;

class TackerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->provider = new TackerServiceProvider;
    }

    public function testDefaultConfig()
    {
        $this->provider->register($pimple = new Pimple);

        $this->assertEquals(array('debug' => true, 'paths' => array(), 'cache_dir' => null), $pimple['tacker.config']);

        $params = array('root_dir' => __DIR__, 'debug' => false, 'tacker.options' => array(
            'cache_dir' => sys_get_temp_dir(),
        ));

        $this->provider->register($pimple = new Pimple($params));

        $this->assertEquals(array('debug' => false, 'paths' => array(__DIR__), 'cache_dir' => sys_get_temp_dir()), $pimple['tacker.config']);
    }

    public function testDebugAndCacheIsSetOnLoader()
    {
        $this->provider->register($pimple = new Pimple);

        $this->assertInternalType('null', $pimple['tacker.loader']->getCacheDir());
        $this->assertTrue($pimple['tacker.loader']->getDebug());

        $this->provider->register($pimple = new Pimple(array(
            'tacker.options' => array('debug' => false, 'cache_dir' => sys_get_temp_dir()),
        )));

        $this->assertEquals(sys_get_temp_dir(), $pimple['tacker.loader']->getCacheDir());
        $this->assertFalse($pimple['tacker.loader']->getDebug());
    }

    /**
     * @dataProvider loadConfigProvider
     */
    public function testLoadConfig($file)
    {
        $this->provider->register($pimple = new Pimple(array(
            'root_dir' => __DIR__ . '/../Fixtures',
        )));

        $pimple['tacker.configurator']->configure($pimple, $file);

        $this->assertEquals('world', $pimple['hello']);
        $this->assertEquals($pimple['root_dir'], $pimple['pimple_normalized']);
        $this->assertEquals(getenv('BRICK_PHPUNIT'), $pimple['env_normalized']);
    }

    public function loadConfigProvider()
    {
        return array(
            array('config.json'),
            array('config.php'),
            array('config.yml'),
            array('config.ini'),
        );
    }
}
