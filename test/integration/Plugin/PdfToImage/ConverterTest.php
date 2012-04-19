<?php

class Plugin_PdfToImage_ConverterTest extends PHPUnit_Framework_TestCase {

    public function testConvertsPdfToJpeg() {
        $this->assertEquals(
            ContentType::jpeg(),
            ContentType::byString(
                self::converter()->convert(
                    Test_Data::pdfDocument()
                )
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function converter() {
        return new Plugin_PdfToImage_Converter(Config::get()->directoryTemp);
    }

}
