<?php

class Integration_Converter_OpenOfficeTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        Test_Util::assertOpenOfficeServiceIsAvailable();
    }

    public function testConvertsOtsToXls() {
        $this->assertEquals(
            ContentType::xls(),
            ContentType::byString(
                self::c(ContentType::xls())->convert(Test_Data::otsTemplate())
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function c(ContentType $targetContentType) {
        return new Converter_OpenOffice($targetContentType, Config::get()->directoryTemp);
    }
}
