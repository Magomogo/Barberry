<?php

class RequestTest extends PHPUnit_Framework_TestCase {

    public function testExtractsId() {
        $this->assertEquals(
            '12345zx',
            self::request('/12345zx.jpg')->id
        );
    }

    public function testUnderstandsOutputContentTypeByExtension() {
        $this->assertEquals(
            ContentType::jpeg(),
            self::request('/12345zx.jpg')->contentType
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function request($uri) {
        return new Request($uri, null);
    }
}
