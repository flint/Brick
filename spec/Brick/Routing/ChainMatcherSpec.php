<?php

namespace spec\Brick\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class ChainMatcherSpec extends \PhpSpec\ObjectBehavior
{
    /**
     * @param Symfony\Component\Routing\Matcher\UrlMatcherInterface $matcher
     * @param Symfony\Component\Routing\RequestContext $context
     */
    function let($matcher, $context)
    {
        $this->beConstructedWith([$matcher]);
    }

    function it_throws_exception_when_no_matcher_matches_route()
    {
        $this->beConstructedWith([]);

        $this->shouldThrow('Symfony\Component\Routing\Exception\ResourceNotFoundException')
            ->duringMatch('/path');

        $this->shouldThrow('Symfony\Component\Routing\Exception\ResourceNotFoundException')
            ->duringMatchRequest(new Request);
    }

    function it_sets_context_before_matching($matcher, $context)
    {
        $matcher->setContext($context)->shouldBeCalled();
        $matcher->match('/pathinfo')->willReturn('resolved');

        $this->setContext($context);

        $this->match('/pathinfo')->shouldReturn('resolved');
        $this->getContext()->shouldReturn($context);
    }

    /**
     * @param Symfony\Component\Routing\Matcher\UrlMatcherInterface $higher
     */
    function it_sorts_matchers_by_priority($higher, $matcher, $context)
    {
        $this->add($higher, 100);

        $this->setContext($context);

        $matcher->match('/path')->shouldNotBeCalled();

        $higher->setContext($context)->shouldBeCalled();
        $higher->match('/path')->willReturn('resolved')
            ->shouldBeCalled();

        $this->match('/path')->shouldReturn('resolved');
    }

    /**
     * @param Symfony\Component\Routing\Matcher\UrlMatcherInterface $first
     */
    function it_saves_method_not_allowed_and_they_take_presedence($first, $matcher, $context)
    {
        $first->setContext($context)->shouldBeCalled();
        $matcher->setContext($context)->shouldBeCalled();

        $first->match('/path')->willThrow('Symfony\Component\Routing\Exception\ResourceNotFoundException');
        $matcher->match('/path')->willThrow(new MethodNotAllowedException([]));

        $this->setContext($context);

        $this->add($first, -100);

        $this->shouldThrow('Symfony\Component\Routing\Exception\MethodNotAllowedException')
            ->duringMatch('/path');
    }
}
