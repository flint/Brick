<?php

namespace Brick;

use Pimple\Container;

interface PimpleAware
{
    public function setPimple(Container $pimple = null);
}
