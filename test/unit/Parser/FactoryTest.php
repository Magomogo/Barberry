<?php

class Parser_FactoryTest extends PHPUnit_Framework_TestCase {

    public function testOpenOfficeSpreadsheetParserIs_Parser_OpenOfficeTemplate() {
        $this->assertInstanceOf('Parser_OpenOfficeTemplate', self::factory()->otsParser());
    }

    public function testOpenOfficeTextParserIs_Parser_OpenOfficeTemplate() {
        $this->assertInstanceOf('Parser_OpenOfficeTemplate', self::factory()->ottParser());
    }

//--------------------------------------------------------------------------------------------------

    private static function factory() {
        return new Parser_Factory();
    }
}
