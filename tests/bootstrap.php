<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Brick\\Tests', __DIR__);

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
