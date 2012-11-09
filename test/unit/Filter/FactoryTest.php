<?php
namespace Barberry\Filter;

class FactoryTest extends \PHPUnit_Framework_TestCase {

    public function testOpenOfficeSpreadsheetParserIs_Parser_OpenOfficeTemplate() {
        $this->assertInstanceOf('Barberry\\Filter\\OpenOfficeTemplate', self::factory()->otsFilter());
    }

    public function testOpenOfficeTextParserIs_Parser_OpenOfficeTemplate() {
        $this->assertInstanceOf('Barberry\\Filter\\OpenOfficeTemplate', self::factory()->ottFilter());
    }

//--------------------------------------------------------------------------------------------------

    private static function factory() {
        return new Factory();
    }
}
