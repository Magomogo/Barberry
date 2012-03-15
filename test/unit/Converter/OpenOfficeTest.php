<?php

class Converter_OpenOfficeTest extends PHPUnit_Framework_TestCase {

    public function testImplementsConverterInterface() {
        $this->assertInstanceOf(
            'Converter_Interface',
            new Converter_OpenOffice(ContentType::xls(), '')
        );
    }
}
