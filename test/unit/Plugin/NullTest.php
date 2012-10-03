<?php
namespace Barberry\Plugin;
use Barberry\Test;

class NullTest extends \PHPUnit_Framework_TestCase {

    public function testDataType() {
        $this->assertInstanceOf('Barberry\\Plugin\\InterfaceConverter', new Null());
    }

    public function testReturnsTheArgument() {
        $c = new Null();
        $this->assertEquals(
            Test\Data::gif1x1(),
            $c->convert(Test\Data::gif1x1())
        );
    }
}
