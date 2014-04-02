<?php

namespace spec\Brick\Routing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ChainUrlGeneratorSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Routing\Generator\UrlGeneratorInterface $first
     * @param Symfony\Component\Routing\Generator\UrlGeneratorInterface $second
     */
    function let($first, $second)
    {
        $this->beConstructedWith(array($first, $second));
    }

    function its_a_url_generator()
    {
        $this->shouldHaveType('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
    }

    function it_rethrows_not_found_exception($first)
    {
        $this->beConstructedWith(array($first));

        $first->generate('route_name', array(), false)
            ->shouldBeCalled()->willThrow('Symfony\Component\Routing\Exception\RouteNotFoundException');

        $this->shouldThrow('Symfony\Component\Routing\Exception\RouteNotFoundException')
            ->duringGenerate('route_name');
    }

    function it_generates_in_a_chain($first, $second)
    {
        $first->generate('route_name', array(), false)
            ->shouldBeCalled()->willThrow('Symfony\Component\Routing\Exception\RouteNotFoundException');

        $second->generate('route_name', array(), false)
            ->shouldBeCalled()->willReturn('this-is-my-url');

        $this->generate('route_name')->shouldReturn('this-is-my-url');
    }

    function it_prioritize_invalid_over_missing($first, $second)
    {
        $first->generate('route_name', array(), false)
            ->willThrow('Symfony\Component\Routing\Exception\MissingMandatoryParametersException');

        $second->generate('route_name', array(), false)
            ->willThrow('Symfony\Component\Routing\Exception\InvalidParameterException');

        $this->shouldThrow('Symfony\Component\Routing\Exception\InvalidParameterException')
            ->duringGenerate('route_name');
    }

    function it_prioritize_missing_over_not_found($first, $second)
    {
        $first->generate('route_name', array(), false)
            ->willThrow('Symfony\Component\Routing\Exception\RouteNotFoundException');

        $second->generate('route_name', array(), false)
            ->shouldBeCalled()->willThrow('Symfony\Component\Routing\Exception\MissingMandatoryParametersException');

        $this->shouldThrow('Symfony\Component\Routing\Exception\MissingMandatoryParametersException')
            ->duringGenerate('route_name');
    }
}
