<?php

namespace Barberry\Direction;

use Barberry\ContentType;
use Barberry\Plugin\NotAvailableException;
use Barberry\Plugin\NullPlugin;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{

    public function testDetectsDirectionClassName(): void
    {
        self::assertInstanceOf(
            'Barberry\\Direction\\DirectionUrlToPhp',
            self::factory()->direction(ContentType::byExtention('url'), ContentType::byExtention('php'))
        );
    }

    public function testThrowExceptionWhenDirectionIsNotAvailable(): void
    {
        $this->expectException(NotAvailableException::class);
        self::factory()->direction(ContentType::byExtention('php'), ContentType::byExtention('jpeg'));
    }

    public function testSameSourceAndDestinationWithoutCommandActivatesNullPlugin(): void
    {
        self::assertInstanceOf(
            NullPlugin::class,
            self::factory()->direction(ContentType::byExtention('jpeg'), ContentType::byExtention('jpeg'))
        );
    }

    public function testSameSourceAndDestinationWithCommandRequiresPlugin(): void
    {
        $this->expectException(NotAvailableException::class);
        self::factory()->direction(ContentType::byExtention('php'), ContentType::byExtention('php'), '12');
    }

    private static function factory(): Factory
    {
        return new Factory();
    }
}

class DirectionUrlToPhp
{

}
