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
}
