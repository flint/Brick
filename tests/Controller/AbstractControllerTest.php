<?php

namespace Brick\Tests\Controller;

use Brick\Tests\Fixtures\PimpleAwareController;
use Brick\Controller\AbstractController;

class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsPimpleAware()
    {
        $this->assertInstanceOf('Brick\PimpleAware', new PimpleAwareController);
        $this->assertInstanceOf('Brick\Controller\AbstractController', new PimpleAwareController);
    }
}
