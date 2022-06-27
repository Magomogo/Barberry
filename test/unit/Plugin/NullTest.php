<?php

namespace Barberry\Plugin;

use Barberry\Test;
use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{
    public function testDataType(): void
    {
        self::assertInstanceOf(InterfaceConverter::class, new NullPlugin());
    }

    public function testReturnsTheArgument(): void
    {
        $c = new NullPlugin();

        self::assertEquals(
            Test\Data::gif1x1(),
            $c->convert(Test\Data::gif1x1())
        );
    }
}
