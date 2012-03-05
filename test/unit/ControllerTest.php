<?php

class ControllerTest extends PHPUnit_Framework_TestCase {

    public function testDataType() {
        $this->assertInstanceOf('Controller_Interface', self::c());
    }

    public function testGETReadsStorage() {
        $storage = $this->getMock('Storage_Interface');
        $storage->expects($this->once())
                ->method('getById')
                ->will($this->returnValue(Test_Data::gif1x1()))
                ->with('123asd');
        $this->c($storage, '123asd', ContentType::createByExtention('gif'))->GET();
    }

    public function testGETReturnsAResponseObject() {
        $this->assertInstanceOf('Response', self::c()->GET());
    }

    public function testGETResponseContainsCorrectContentType() {
        $response = $this->c(null, null, ContentType::gif())->GET();
        $this->assertEquals(ContentType::gif(), $response->contentType);
    }

    public function testPOSTResponseIsJson() {
        $response = self::c()->requestDispatched(null, null, '123')->POST();
        $this->assertEquals(ContentType::json(), $response->contentType);
    }

    public function testDELETEResponseIsJson() {
        $response = self::c()->DELETE();
        $this->assertEquals(ContentType::json(), $response->contentType);
    }

    public function testPOSTReturnsEntityIdAtSaveToStorage() {
        $storage = $this->getMock('Storage_Interface');
        $storage->expects($this->once())->method('save')->will($this->returnValue('12345xz'));

        $this->assertEquals(
            new Response(ContentType::json(), json_encode(array('id'=>'12345xz'))),
            $this->c($storage)->requestDispatched(null, null, '123')->POST()
        );
    }

    public function testSavesPostedContentToTheStorage() {
        $storage = $this->getMock('Storage_Interface');
        $storage->expects($this->once())->method('save')->with('0101010111');
        $controller = $this->c($storage);
        $controller->requestDispatched(null, null, '0101010111');
        $controller->POST();
    }

    public function testThrowsNullPostValueWhenNoContentPosted() {
        $this->setExpectedException('Controller_NullPostException');
        self::c()->POST();
    }

    public function testThrowsNotFoundExceptionWhenUnknownMethodIsCalled() {
        $this->setExpectedException('Controller_NotFoundException');
        self::c()->PUT();
    }

    public function testThrowsNotFoundExceptionWhenStorageHasNoContentForRequestedId() {
        $this->setExpectedException('Controller_NotFoundException');
        self::c(Test_Stub::create('Storage_Interface', 'getById', null))->GET();
    }

//--------------------------------------------------------------------------------------------------

    private static function c(Storage_Interface $storage = null, $entityId = null, $outputContentType = null) {
        $c = new Controller($storage ?: self::aGifStorageStub());
        $c->requestDispatched($entityId, $outputContentType ?: ContentType::gif());
        return $c;
    }

    private static function aGifStorageStub() {
        return Test_Stub::create('Storage_Interface', 'getById', Test_Data::gif1x1());
    }
}
