<?php

class Direction_AbstractTest extends PHPUnit_Framework_TestCase {

    public function testTransfersStringCommandToConverter() {
        $direction = new TestDirection('string_command');
        $direction->getConverter()->shouldReceive('convert')->with('010101', 'string_command');

        $direction->convert('010101');
    }
}

//==================================================================================================

class TestDirection extends Direction_Abstract {
    public function init() {
        $this->converter = Mockery::mock();
    }

    /**
     * @return Mockery\MockInterface
     */
    public function getConverter() {
        return $this->converter;
    }
}