<?php

class Integration_Plugin_OpenOffice_ConverterTest extends PHPUnit_Framework_TestCase {

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
        return new Plugin_OpenOffice_Converter($targetContentType, Config::get()->directoryTemp);
    }
}
