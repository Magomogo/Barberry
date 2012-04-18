<?php

class Integration_ConvertDirectionTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        Test_Util::assertOpenOfficeServiceIsAvailable();
    }

    public function testOts2Xls() {
        $this->assertEquals(
            ContentType::xls(),
            ContentType::byString(self::convert(Test_Data::otsTemplate(), ContentType::xls()))
        );
    }

    public function testOds2Xls() {
        $this->assertEquals(
            ContentType::xls(),
            ContentType::byString(self::convert(Test_Data::odsSpreadsheet(), ContentType::xls()))
        );
    }

    public function testOtt2Doc() {
        $this->assertEquals(
            ContentType::doc(),
            ContentType::byString(self::convert(Test_Data::ottTemplate(), ContentType::doc()))
        );
    }

    public function testOdt2Doc() {
        $this->assertEquals(
            ContentType::doc(),
            ContentType::byString(self::convert(Test_Data::odtDocument(), ContentType::doc()))
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function convert($dataToConvert, ContentType $targetContentType) {
        $dir = new Direction_Factory($dataToConvert, $targetContentType);
        return $dir->direction()->convert($dataToConvert);
    }

}
