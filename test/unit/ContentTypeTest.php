<?php

class ContentTypeTest extends PHPUnit_Framework_TestCase {

    public function testIsJpegCreatedByExtention() {
        $this->assertEquals(
            'jpg',
            ContentType::byExtention('jpg')->standartExtention()
        );
    }

    public function testContentTypeHasStandartExtension() {
        ContentType::byExtention('jpg')->standartExtention();
    }

    public function testIsPhpCreatedByContentTypeString() {
        $this->assertEquals(
            'php',
            ContentType::byString(file_get_contents(__FILE__))->standartExtention()
        );
    }

    public function testMagicallyBecomesAString() {
        $this->assertEquals('image/jpeg', strval(ContentType::jpeg()));
    }
}