<?php

class Plugin_OpenOffice_ConverterTest extends PHPUnit_Framework_TestCase {

    public function testImplementsConverterInterface() {
        $this->assertInstanceOf(
            'Plugin_Interface_Converter',
            new Plugin_OpenOffice_Converter(ContentType::xls(), '')
        );
    }
}
