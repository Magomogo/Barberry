<?php

class Plugin_Pdf_ConverterTest extends PHPUnit_Framework_TestCase {

    public function testConvertsPdfToJpeg() {
        $this->assertEquals(
            ContentType::jpeg(),
            ContentType::byString(
                self::converter(ContentType::jpeg())->convert(
                    Test_Data::pdfDocument(),
                    self::emptyCommand()
                )
            )
        );
    }

    public function testConvertsPdfToText() {
        $this->assertEquals(
            ContentType::txt(),
            ContentType::byString(
                self::converter(ContentType::txt())->convert(
                    Test_Data::pdfDocument(),
                    self::emptyCommand()
                )
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function converter(ContentType $targetContentType) {
        return new Plugin_Pdf_Converter($targetContentType, Config::get()->directoryTemp);
    }

    private static function emptyCommand() {
        $command = new Plugin_Pdf_Command();
        return $command->configure('');
    }

}
