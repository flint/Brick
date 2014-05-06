<?php

namespace Brick\Controller;

use Brick\PimpleAware;
use Pimple\Container;

abstract class AbstractController implements PimpleAware
{
    protected $pimple;

    public function setPimple(Container $pimple = null)
    {
        $this->pimple = $pimple;
    }
}
