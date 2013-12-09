<?php

namespace Brick\Controller;

use Brick\PimpleAware;
use Pimple;

abstract class AbstractController implements PimpleAware
{
    protected $pimple;

    public function setPimple(Pimple $pimple = null)
    {
        $this->pimple = $pimple;
    }
}
