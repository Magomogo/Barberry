<?php
namespace Barberry\Parser;

class FactoryTest extends \PHPUnit_Framework_TestCase {

    public function testOpenOfficeSpreadsheetParserIs_Parser_OpenOfficeTemplate() {
        $this->assertInstanceOf('Barberry\\Parser\\OpenOfficeTemplate', self::factory()->otsParser());
    }

    public function testOpenOfficeTextParserIs_Parser_OpenOfficeTemplate() {
        $this->assertInstanceOf('Barberry\\Parser\\OpenOfficeTemplate', self::factory()->ottParser());
    }

//--------------------------------------------------------------------------------------------------

    private static function factory() {
        return new Factory();
    }
}
