<?php

namespace Barberry\Direction;

use Barberry\ContentType;
use Barberry\Plugin\NotAvailableException;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{

    public function testDetectsDirectionClassName() {
        $this->assertInstanceOf(
            'Barberry\\Direction\\DirectionUrlToPhp',
            self::factory()->direction(ContentType::byExtention('url'), ContentType::byExtention('php'))
        );
    }

    public function testThrowExceptionWhenDirectionIsNotAvailable() {
        $this->expectException(NotAvailableException::class);
        self::factory()->direction(ContentType::byExtention('php'), ContentType::byExtention('jpeg'));
    }

    public function testSameSourceAndDestinationWithoutCommandActivatesNullPlugin() {
        $this->assertInstanceOf(
            'Barberry\Plugin\NullPlugin',
            self::factory()->direction(ContentType::byExtention('jpeg'), ContentType::byExtention('jpeg'))
        );
    }

    public function testSameSourceAndDestinationWithCommandRequiresPlugin() {
        $this->expectException(NotAvailableException::class);
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

class DirectionUrlToPhp
{

}
