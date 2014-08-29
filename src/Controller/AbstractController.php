<?php

namespace Brick\Controller;

use Brick\PimpleAware;
use Pimple\Container;

abstract class AbstractController implements PimpleAware
{
    protected $pimple;

    public function setContainer(Container $pimple = null)
    {
        $this->pimple = $pimple;
    }
}
