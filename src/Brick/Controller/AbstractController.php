<?php

namespace Brick\Controller;

abstract class AbstractController implements PimpleAware
{
    protected $pimple;

    public function setPimple(Pimple $pimple)
    {
        $this->pimple = $pimple;
    }
}
