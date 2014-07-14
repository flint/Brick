<?php

namespace Brick;

use Pimple\Container;

interface PimpleAware
{
    public function setContainer(Container $pimple = null);
}
