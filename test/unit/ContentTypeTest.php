<?php

class ContentTypeTest extends PHPUnit_Framework_TestCase {

    public function testIsJpegCreatedByExtention() {
        $this->assertEquals(
            'jpg',
            ContentType::createByExtention('jpeg')->standartExtention()
        );
    }

    public function testContentTypeHasStandartExtension() {
        ContentType::createByExtention('jpeg')->standartExtention();
    }

    public function testIsPhpCreatedByContentTypeString() {
        $this->assertEquals(
            'php',
            ContentType::createByContentTypeString('text/x-php; charset=us-ascii')->standartExtention()
        );
    }

    public function testMagicallyBecomesAString() {
        $this->assertEquals('image/jpeg', strval(ContentType::jpeg()));
    }
}