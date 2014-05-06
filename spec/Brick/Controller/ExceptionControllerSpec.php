<?php

namespace spec\Brick\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExceptionControllerSpec extends ObjectBehavior
{
    /**
     * @param Twig_Environment $twig
     */
    function let($twig)
    {
        $this->beConstructedWith($twig);
    }

    /**
     * @param Twig_Template $template
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\Debug\Exception\FlattenException $exception
     */
    function it_loops_through_templates_and_renders_response($template, $request, $exception, $twig)
    {
        $templates = [
            'Exception/error500.json.twig',
            'Exception/error.json.twig',
            'Exception/error.html.twig',
        ];

        $exception->getStatusCode()->willReturn(500);
        $twig->resolveTemplate($templates)->willReturn($template);

        $template->render(['status_code' => 500, 'status_text' => 'Internal Server Error', 'exception' => $exception])
            ->shouldBeCalled()->willReturn('rendered_template');

        $template->getTemplateName()->willReturn('error500.json.twig');

        $response = $this($request, $exception, 'json');
        $response->getStatusCode()->shouldReturn(500);
        $response->getContent()->shouldReturn('rendered_template');
    }

    /**
     * @param Twig_Template $template
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\Debug\Exception\FlattenException $exception
     */
    function it_sets_request_to_html_for_html_template($template, $request, $exception, $twig)
    {
        $exception->getStatusCode()->willReturn(500);
        $twig->resolveTemplate(Argument::any())->willReturn($template);

        $template->render(Argument::any())->willReturn('rendered_template');

        $template->getTemplateName()->willReturn('error.html.twig');

        $request->setRequestFormat('html')->shouldBeCalled();

        $this($request, $exception, 'html');
    }

    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\Debug\Exception\FlattenException $exception
     */
    function it_renders_default_exception_response_when_templates_is_missing($request, $exception, $twig)
    {
        $twig->resolveTemplate(Argument::any())->willThrow(new \Twig_Error_Loader(''));

        $exception->getStatusCode()->willReturn(500);
        $exception->getHeaders()->willReturn([]);

        $request->setRequestFormat('html')->shouldBeCalled();

        $response = $this($request, $exception, 'json');
        $response->getStatusCode()->shouldReturn(500);
    }
}
