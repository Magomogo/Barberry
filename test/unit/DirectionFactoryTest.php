<?php

class DirectionFactoryTest extends PHPUnit_Framework_TestCase {

    public function testDetectsDirectionClassName() {
        $this->assertAttributeEquals(
            'PhpToJpgDirection',
            'directionClassName',
            self::phpToSomethingDirection()
        );
    }

    public function testThrowExceptionWhenDirectionIsNotAvailable() {
        $this->setExpectedException('Plugin_NotAvailableException');

        self::phpToSomethingDirection('gif')->direction();
    }

    public function testSameSourceAndDestinationWithoutCommandActivatesNullPlugin() {
        $this->assertInstanceOf('Plugin_Null', self::phpToSomethingDirection('php')->direction());
    }

    public function testSameSourceAndDestinationWitCommandRequiresPlugin() {
        $this->setExpectedException('Plugin_NotAvailableException');

        $this->assertInstanceOf('Plugin_Null', self::phpToSomethingDirection('php')->direction(
            Test_Stub::create('Plugin_Interface_Command')
        ));
    }

//--------------------------------------------------------------------------------------------------

    private static function phpToSomethingDirection($ext='jpg') {
        return new Direction_Factory(
            file_get_contents(__FILE__),
            ContentType::byExtention($ext)
        );
    }
}