<?php

namespace Brick\Routing;

use Symfony\Component\Routing\Route;
use Brick\Annotation\Route as BrickRoute;

class AnnotationClassLoader extends \Symfony\Component\Routing\Loader\AnnotationClassLoader
{
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        if ($annot instanceof BrickRoute) {
            $route->setDefault('_controller', $annot->getService() . ':' . $method->getName());
        } else {
            $route->setDefault('_controller', $class->getName() . '::' . $method->getName());
        }
    }
}
