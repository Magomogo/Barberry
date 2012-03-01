<?php

class ControllerTest extends PHPUnit_Framework_TestCase {

    public function testGETReadsStorage() {
        $storage = $this->getMock('Storage_Interface');
        $storage->expects($this->once())->method('getById')->with('123asd');
        $this->c($storage, '123asd')->GET();
    }

    public function testGETReturnsAResponseObject() {
        $this->assertInstanceOf('Response', $this->c()->GET());
    }

    public function testGETResponseContainsCorrectContentType() {
        $response = $this->c(null, null, self::jpegContentType())->GET();
        $this->assertEquals(self::jpegContentType(), $response->contentType);
    }

    public function testPOSTResponseIsJson() {
        $response = $this->c()->POST();
        $this->assertEquals(self::jsonContentType(), $response->contentType);
    }

    public function testDELETEResponseIsJson() {
        $response = $this->c()->DELETE();
        $this->assertEquals(self::jsonContentType(), $response->contentType);
    }

    public function testPOSTReturnEntityIdAtSaveToStorage() {
        $storage = $this->getMock('Storage_Interface');
        $storage->expects($this->once())->method('save')->will($this->returnValue('12345xz'));

        $this->assertEquals(
            new Response(self::jsonContentType(), json_encode(array('id'=>'12345xz'))),
            $this->c($storage)->POST()
        );
    }

//--------------------------------------------------------------------------------------------------

    private function c(Storage_Interface $storage = null, $entityId = null, $outputContentType = null) {
        return new Controller(
            $storage ?: $this->getMock('Storage_Interface'),
            $entityId,
            $outputContentType
        );
    }

    private static function jpegContentType() {
        return ContentType::createByExtention('jpg');
    }

    private static function jsonContentType() {
        return ContentType::createByExtention('json');
    }
}
