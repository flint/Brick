<?php

namespace Brick\Controller;

use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController
{
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, FlattenException $exception, $format)
    {
        $statusCode = $exception->getStatusCode();

        try {
            $template = $this->twig->resolveTemplate([
                'Exception/error' . $statusCode . '.' . $format . '.twig',
                'Exception/error.' . $format . '.twig',
                'Exception/error.html.twig',
            ]);
        } catch (\Twig_Error_Loader $e) {
            $request->setRequestFormat('html');

            $content = (new ExceptionHandler(false))->getHtml($exception);

            return new Response($content, $exception->getStatusCode(), $exception->getHeaders());
        }

        // We cannot find a template that matches the precise format so we will default
        // to html as previously in the ExceptionHandler
        if (substr($template->getTemplateName(), -9) == 'html.twig') {
            $request->setRequestFormat('html');
        }

        $variables = [
            'exception'   => $exception,
            'status_code' => $statusCode,
            'status_text' => isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : '',
        ];

        return new Response($template->render($variables), $statusCode);
    }
}
