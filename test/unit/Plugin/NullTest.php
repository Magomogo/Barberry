<?php

class Plugin_NullTest extends PHPUnit_Framework_TestCase {

    public function testDataType() {
        $this->assertInstanceOf('Plugin_Interface_Converter', new Plugin_Null());
    }

    public function testReturnsTheArgument() {
        $c = new Plugin_Null();
        $this->assertEquals(
            Test_Data::gif1x1(),
            $c->convert(Test_Data::gif1x1())
        );
    }
}
