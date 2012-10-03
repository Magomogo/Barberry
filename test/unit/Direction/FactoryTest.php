<?php
namespace Barberry\Direction;
use Barberry\ContentType;
use Barberry\Plugin;

class FactoryTest extends \PHPUnit_Framework_TestCase {

    public function testDetectsDirectionClassName() {
        $this->assertAttributeEquals(
            'Barberry\\Direction\\PhpToJpgDirection',
            'directionClassName',
            self::phpToSomethingDirection()
        );
    }

    public function testThrowExceptionWhenDirectionIsNotAvailable() {
        $this->setExpectedException('Barberry\Plugin\NotAvailableException');

        self::phpToSomethingDirection('gif')->direction();
    }

    public function testSameSourceAndDestinationWithoutCommandActivatesNullPlugin() {
        $this->assertInstanceOf('Barberry\Plugin\Null', self::phpToSomethingDirection('php')->direction());
    }

    public function testSameSourceAndDestinationWithCommandRequiresPlugin() {
        $this->setExpectedException('Barberry\Plugin\NotAvailableException');
        self::phpToSomethingDirection('php')->direction('12');
    }

//--------------------------------------------------------------------------------------------------

    private static function phpToSomethingDirection($ext='jpg') {
        return new Factory(
            file_get_contents(__FILE__),
            ContentType::byExtention($ext)
        );
    }
}
