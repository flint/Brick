<?php

namespace Brick\Tests\Fixtures;

use Pimple\Container;

class PimpleAwareController extends \Brick\Controller\AbstractController
{
    public function setContainer(Container $pimple = null)
    {
    }
}
