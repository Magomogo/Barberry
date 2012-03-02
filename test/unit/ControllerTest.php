<?php

class ControllerTest extends PHPUnit_Framework_TestCase {

    public function testDataType() {
        $this->assertInstanceOf('Controller_Interface', $this->c());
    }

    public function testGETReadsStorage() {
        $storage = $this->aGifStorageStub();
        $storage->expects($this->once())->method('getById')->with('123asd');
        $this->c($storage, '123asd', ContentType::createByExtention('gif'))->GET();
    }

    public function testGETReturnsAResponseObject() {
        $this->assertInstanceOf('Response', $this->c()->GET());
    }

    public function testGETResponseContainsCorrectContentType() {
        $response = $this->c(null, null, ContentType::gif())->GET();
        $this->assertEquals(ContentType::gif(), $response->contentType);
    }

    public function testPOSTResponseIsJson() {
        $response = $this->c()->POST();
        $this->assertEquals(ContentType::json(), $response->contentType);
    }

    public function testDELETEResponseIsJson() {
        $response = $this->c()->DELETE();
        $this->assertEquals(ContentType::json(), $response->contentType);
    }

    public function testPOSTReturnEntityIdAtSaveToStorage() {
        $storage = $this->getMock('Storage_Interface');
        $storage->expects($this->once())->method('save')->will($this->returnValue('12345xz'));

        $this->assertEquals(
            new Response(ContentType::json(), json_encode(array('id'=>'12345xz'))),
            $this->c($storage)->POST()
        );
    }

//--------------------------------------------------------------------------------------------------

    private function c(Storage_Interface $storage = null, $entityId = null, $outputContentType = null) {
        $c = new Controller($storage ?: $this->aGifStorageStub());
        $c->requestDispatched($entityId, $outputContentType ?: ContentType::gif());
        return $c;
    }

    private function aGifStorageStub() {
        $stub = $this->getMock('Storage_Interface');
        $stub->expects($this->any())->method('getById')->will($this->returnValue(
            Test_Data::gif1x1()
        ));
        return $stub;
    }
}
