<?php

class ConvertDirectionTest extends PHPUnit_Framework_TestCase {

    public function testDetectsDirection() {
        $this->assertAttributeEquals(
            'phpToJpg',
            'factoryMethod',
            self::phpToImageDirection()
        );
    }

    public function testCanInitConverterWithFactory() {
        $factory = $this->getMock('Test_FakeConverterFactory');
        $factory->expects($this->once())->method(('phpToJpg'));

        self::phpToImageDirection()->initConverter($factory);
    }

    public function testThrowExceptionWhenDirectionIsNotAvailable() {
        $this->setExpectedException('Converter_NotAvailableException');

        self::phpToImageDirection('gif')->initConverter(
            $this->getMock('Test_FakeConverterFactory')
        );
    }

    private static function phpToImageDirection($imageExt='jpg') {
        return new ConvertDirection(
            file_get_contents(__FILE__),
            ContentType::createByExtention($imageExt)
        );
    }

}


interface Test_FakeConverterFactory {
    public function phpToJpg();
}