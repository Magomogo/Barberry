<?php

class Plugin_PdfToImage_CommandTest extends PHPUnit_Framework_TestCase {

    public function testDefaultWidthDefined() {
        $this->assertEquals(
            800,
            self::command()->width()
        );
    }

    public function testCommandStringContainsTheWidth() {
        $this->assertEquals(
            150,
            self::command('150')->width()
        );
    }

    public function testWidthIsLimitedWithMinimalValue() {
        $this->assertEquals(
            10,
            self::command('0')->width()
        );
    }

    public function testWidthIsLimitedWithMaximalValue() {
        $this->assertEquals(
            2000,
            self::command('500000')->width()
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function command($commandString = null) {
        return new Plugin_PdfToImage_Command($commandString);
    }
}
