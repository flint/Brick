<?php

namespace spec\Brick;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ControllerResolverSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Controller\ControllerResolver $resolver
     * @param Pimple\Container $pimple
     */
    function let($resolver, $pimple)
    {
        $this->beConstructedWith($resolver, $pimple);
    }

    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    function it_proxies_get_arguments_to_composed_resolver($request, $resolver)
    {
        $resolver->getArguments($request, 'controller')->shouldBeCalled()->willReturn([]);

        $this->getArguments($request, 'controller')->shouldReturn([]);
    }

    /**
     * @param Brick\PimpleAware $controller
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    function it_sets_pimple_on_controller_when_it_is_pimpleaware($controller, $request, $resolver, $pimple)
    {
        $resolver->getController($request)->willReturn([$controller, 'indexAction']);

        $controller->setPimple($pimple)->shouldBeCalled();

        $this->getController($request)->shouldReturn([$controller, 'indexAction']);
    }

    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    function it_does_not_work_with_functions($request, $resolver)
    {
        $resolver->getController($request)->willReturn('var_dump');

        $this->getController($request)->shouldReturn('var_dump');
    }
}
