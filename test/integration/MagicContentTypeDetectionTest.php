<?php

class MagicContentTypeDetectionTest extends PHPUnit_Framework_TestCase {

    public function testGif() {
        $this->assertEquals(
            ContentType::gif(),
            ContentType::byString(Test_Data::gif1x1())
        );
    }

    public function testOpenDocumentTextTempate() {
        $this->assertEquals(
            ContentType::ott(),
            ContentType::byString(Test_Data::ottTemplate())
        );
    }
}
