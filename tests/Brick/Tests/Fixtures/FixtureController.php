<?php

namespace Brick\Tests\Fixtures;

class FixtureController
{
    public function helloAction($format)
    {
        return 'world from ' . $format;
    }
}
