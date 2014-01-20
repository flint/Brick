<?php

namespace Brick\Routing;

use Symfony\Component\Routing\Route;

class AnnotationClassLoader extends \Symfony\Component\Routing\Loader\AnnotationClassLoader
{
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        $route->setDefault('_controller', $class->getName() . '::' . $method->getName());
    }
}
