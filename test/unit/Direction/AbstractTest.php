<?php

class Direction_AbstractTest extends PHPUnit_Framework_TestCase {

    public function testTransfersStringCommandToConverter() {
        $direction = new TestDirection('string_command');
        $direction->getConverter()->shouldReceive('convert')
            ->with('010101', anInstanceOf('Plugin_Interface_Command')
        );

        $direction->convert('010101');
    }
}

//==================================================================================================

class TestDirection extends Direction_Abstract {
    public function init($commandString = null) {
        $this->converter = Mockery::mock();
        $this->command = Mockery::mock('Plugin_Interface_Command');
    }

    /**
     * @return Mockery\MockInterface
     */
    public function getConverter() {
        return $this->converter;
    }
}