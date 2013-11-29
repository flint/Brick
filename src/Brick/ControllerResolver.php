<?php

namespace Brick;

use Pimple;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolver implements ControllerResolverInterface
{
    protected $pimple;
    protected $resolver;

    public function __construct(ControllerResolverInterface $resolver, Pimple $pimple)
    {
        $this->resolver = $resolver;
        $this->pimple = $pimple;
    }

    public function getController(Request $request)
    {
        $controller = $this->resolver->getController($request);

        if (!is_array($controller)) {
            return $controller;
        }

        if ($controller[0] instanceof PimpleAware) {
            $controller[0]->setPimple($this->pimple);
        }

        return $controller;
    }

    public function getArguments(Request $request, $controller)
    {
        return $this->resolver->getArguments($request, $controller);
    }
}
