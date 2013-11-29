<?php

namespace Brick\Routing;

use Symfony\Component\Routing\RouteCollection;

class NullLoader extends \Symfony\Component\Config\Loader\Loader
{
    public function load($resource, $type = null)
    {
        return new RouteCollection;
    }

    public function supports($resource, $type = null)
    {
        return is_null($resource);
    }
}
