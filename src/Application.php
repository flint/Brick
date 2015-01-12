<?php

namespace Brick;

use Brick\Provider\TackerServiceProvider;
use Brick\Provider\RoutingServiceProvider;
use Brick\Provider\ExceptionServiceProvider;

class Application extends \Silex\Application
{
    public function __construct($rootDir, $debug = true, array $values = [])
    {
        parent::__construct(['root_dir' => $rootDir, 'debug' => $debug] + $values);

        $this->register(new ExceptionServiceProvider);
        $this->register(new RoutingServiceProvider);
        $this->register(new TackerServiceProvider);
    }

    public function configure($resource)
    {
        $this['tacker.configurator']->configure($this, $resource);
    }
}
