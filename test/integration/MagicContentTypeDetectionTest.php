<?php

class MagicContentTypeDetectionTest extends PHPUnit_Framework_TestCase {

    public function testGif() {
        $this->assertEquals(
            ContentType::gif(),
            ContentType::byString(Test_Data::gif1x1())
        );
    }

    public function testOpenDocumentTextTempate() {
        $this->assertEquals(
            ContentType::ott(),
            ContentType::byString(Test_Data::ottTemplate())
        );
    }

    public function testOpenDocumentSpreadsheetTempate() {
        $this->assertEquals(
            ContentType::ots(),
            ContentType::byString(Test_Data::otsTemplate())
        );
    }

    public function testMicrosoftOfficeSpreadsheet() {
        $this->assertEquals(
            ContentType::xls(),
            ContentType::byString(Test_Data::xlsSpreadsheet())
        );
    }

    public function testOpenOfficeSpreadsheet() {
        $this->assertEquals(
            ContentType::ods(),
            ContentType::byString(Test_Data::odsSpreadsheet())
        );
    }

    public function testMicrosoftOfficeDocument() {
        $this->assertEquals(
            ContentType::doc(),
            ContentType::byString(Test_Data::docDocument())
        );
    }

    public function testOpenOfficeDocument() {
        $this->assertEquals(
            ContentType::odt(),
            ContentType::byString(Test_Data::odtDocument())
        );
    }

    public function testPortableDocumentFormat() {
        $this->assertEquals(
            ContentType::pdf(),
            ContentType::byString(Test_Data::pdfDocument())
        );
    }
}
