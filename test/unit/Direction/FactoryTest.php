<?php

class Direction_FactoryTest extends PHPUnit_Framework_TestCase {

    public function testCreatesNullPluginForSameContentTypes() {
        $f = new Direction_Factory('123', ContentType::txt());
        $this->assertInstanceOf('Plugin_Null', $f->direction());
    }
}
