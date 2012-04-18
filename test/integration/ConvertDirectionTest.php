<?php

class Integration_ConvertDirectionTest extends PHPUnit_Framework_TestCase {

    public function testOts2Xls() {
        Test_Util::assertOpenOfficeServiceIsAvailable();

        $this->assertEquals(
            ContentType::xls(),
            ContentType::byString(self::convert(Test_Data::otsTemplate(), ContentType::xls()))
        );
    }

    public function testOds2Xls() {
        Test_Util::assertOpenOfficeServiceIsAvailable();

        $this->assertEquals(
            ContentType::xls(),
            ContentType::byString(self::convert(Test_Data::odsSpreadsheet(), ContentType::xls()))
        );
    }

    public function testOtt2Doc() {
        Test_Util::assertOpenOfficeServiceIsAvailable();

        $this->assertEquals(
            ContentType::doc(),
            ContentType::byString(self::convert(Test_Data::ottTemplate(), ContentType::doc()))
        );
    }

    public function testOdt2Doc() {
        Test_Util::assertOpenOfficeServiceIsAvailable();

        $this->assertEquals(
            ContentType::doc(),
            ContentType::byString(self::convert(Test_Data::odtDocument(), ContentType::doc()))
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function convert($dataToConvert, ContentType $targetContentType) {
        $dir = new DirectionFactory($dataToConvert, $targetContentType);
        return $dir->direction()->convert($dataToConvert);
    }

}
