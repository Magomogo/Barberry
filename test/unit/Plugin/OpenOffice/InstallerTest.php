<?php

class Plugin_OpenOffice_InstallerTest extends PHPUnit_Framework_TestCase {

    public function testInstallsOdtToDocDirection() {
        $composer = Mockery::mock('Direction_Composer');
        $composer->shouldReceive('writeClassDeclaration')->with(
            equalTo(ContentType::odt()),
            equalTo(ContentType::doc()),
            containsString('new Plugin_OpenOffice_Converter (ContentType::doc(),')
        )->once();
        $composer->shouldReceive('writeClassDeclaration');

        self::installer()->install($composer);
    }

    public function testInstallsOtsToXlsDirection() {
        $composer = Mockery::mock('Direction_Composer');
        $composer->shouldReceive('writeClassDeclaration')->with(
            equalTo(ContentType::ots()),
            equalTo(ContentType::xls()),
            containsString('new Plugin_OpenOffice_Converter (ContentType::xls(),')
        )->once();
        $composer->shouldReceive('writeClassDeclaration');

        self::installer()->install($composer);
    }

    public function testInstallsOttToPdfDirection() {
        $composer = Mockery::mock('Direction_Composer');
        $composer->shouldReceive('writeClassDeclaration')->with(
            equalTo(ContentType::ott()),
            equalTo(ContentType::pdf()),
            containsString('new Plugin_OpenOffice_Converter (ContentType::pdf(),')
        )->once();
        $composer->shouldReceive('writeClassDeclaration');

        self::installer()->install($composer);
    }

//--------------------------------------------------------------------------------------------------

    private static function installer() {
        return new Plugin_OpenOffice_Installer('');
    }
}
