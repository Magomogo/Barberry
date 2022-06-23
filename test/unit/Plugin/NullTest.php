<?php

namespace Barberry\Plugin;

use Barberry\Test;
use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{
    public function testDataType() {
        $this->assertInstanceOf('Barberry\\Plugin\\InterfaceConverter', new NullPlugin());
    }

    public function testReturnsTheArgument() {
        $c = new NullPlugin();
        $this->assertEquals(
            Test\Data::gif1x1(),
            $c->convert(Test\Data::gif1x1())
        );
    }
}
