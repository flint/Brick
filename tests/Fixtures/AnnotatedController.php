<?php

namespace Brick\Tests\Fixtures;

use Symfony\Component\Routing\Annotation\Route;

class AnnotatedController
{
    /**
     * @Route("/hello", defaults={"format"="file annotation"})
     */
    public function helloAction($format)
    {
        return 'world from ' . $format;
    }
}
