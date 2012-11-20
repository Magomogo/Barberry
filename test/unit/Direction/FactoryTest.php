<?php
namespace Barberry\Direction;
use Barberry\ContentType;
use Barberry\Plugin;

class FactoryTest extends \PHPUnit_Framework_TestCase {

    public function testDetectsDirectionClassName() {
        $this->assertInstanceOf(
            'Barberry\\Direction\\UrlToPhpDirection',
            self::factory()->direction(ContentType::byExtention('url'), ContentType::byExtention('php'))
        );
    }

    public function testThrowExceptionWhenDirectionIsNotAvailable() {
        $this->setExpectedException('Barberry\Plugin\NotAvailableException', 'text/x-php to image/jpeg');
        self::factory()->direction(ContentType::byExtention('php'), ContentType::byExtention('jpeg'));
    }

    public function testSameSourceAndDestinationWithoutCommandActivatesNullPlugin() {
        $this->assertInstanceOf(
            'Barberry\Plugin\Null',
            self::factory()->direction(ContentType::byExtention('jpeg'), ContentType::byExtention('jpeg'))
        );
    }

    public function testSameSourceAndDestinationWithCommandRequiresPlugin() {
        $this->setExpectedException('Barberry\Plugin\NotAvailableException');
        self::factory()->direction(ContentType::byExtention('php'), ContentType::byExtention('php'), '12');
    }

//--------------------------------------------------------------------------------------------------

    private static function factory($ext='jpg') {
        return new Factory(
            file_get_contents(__FILE__),
            ContentType::byExtention($ext)
        );
    }
}

class UrlToPhpDirection
{

}