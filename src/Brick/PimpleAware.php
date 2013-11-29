<?php

namespace Brick;

use Pimple;

interface PimpleAware
{
    public function setPimple(Pimple $pimple = null);
}
