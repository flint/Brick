<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('router', new Route('/hello', [
    '_controller' => 'Brick\Tests\Fixtures\AnnotatedController::helloAction',
    'format' => 'php',
]));

return $collection;
