<?php

class ConvertDirectionTest extends PHPUnit_Framework_TestCase {

    public function testDetectsDirection() {
        $this->assertAttributeEquals(
            'phpToJpg',
            'factoryMethod',
            self::phpToSomethingDirection()
        );
    }

    public function testCanInitConverterWithFactory() {
        $factory = $this->getMock('Test_FakeConverterFactory');
        $factory->expects($this->once())->method(('phpToJpg'));

        self::phpToSomethingDirection()->initConverter($factory);
    }

    public function testThrowExceptionWhenDirectionIsNotAvailable() {
        $this->setExpectedException('Converter_NotAvailableException');

        self::phpToSomethingDirection('gif')->initConverter(
            $this->getMock('Test_FakeConverterFactory')
        );
    }

    public function testSameSourceAndDestinationActivatesNullConverter() {
        $factory = $this->getMock('Test_FakeConverterFactory');
        $factory->expects($this->once())->method(('nullConverter'));

        self::phpToSomethingDirection('php')->initConverter($factory);
    }

//--------------------------------------------------------------------------------------------------

    private static function phpToSomethingDirection($ext='jpg') {
        return new ConvertDirection(
            file_get_contents(__FILE__),
            ContentType::byExtention($ext)
        );
    }

}

interface Test_FakeConverterFactory {
    public function phpToJpg();
    public function nullConverter();
}