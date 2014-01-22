<?php

namespace spec\Brick\Annotation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RouteSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array());
    }

    function it_contains_service_name()
    {
        $this->setService('service.name');

        $this->getService()->shouldReturn('service.name');
    }
}
