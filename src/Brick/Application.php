<?php

namespace Brick;

use Brick\Provider\TackerServiceProvider;
use Brick\Provider\RoutingServiceProvider;
use Brick\provider\ExceptionServiceProvider;

class Application extends \Silex\Application
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->register(new RoutingServiceProvider);
        $this->register(new TackerServiceProvider);
        $this->register(new ExceptionServiceProvider);
    }

    public function configure($resource)
    {
        $this['tacker.configurator']->configure($this, $resource);
    }
}
