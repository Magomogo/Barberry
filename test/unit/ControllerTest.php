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
        $this->c(new Request('/123asd.gif'), $storage)->GET();
    }

    public function testGETReturnsAResponseObject() {
        $this->assertInstanceOf('Response', self::c()->GET());
    }

    public function testGETResponseContainsCorrectContentType() {
        $response = $this->c(null, null, ContentType::gif())->GET();
        $this->assertEquals(ContentType::gif(), $response->contentType);
    }

    public function testPOSTResponseIsJson() {
        $this->assertEquals(
            ContentType::json(),
            self::c(self::binaryRequest())->POST()->contentType
        );
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
            $this->c(self::binaryRequest(), $storage)->POST()
        );
    }

    public function testSavesPostedContentToTheStorage() {
        $storage = $this->getMock('Storage_Interface');
        $storage->expects($this->once())->method('save')->with('0101010111');
        $controller = $this->c(self::binaryRequest(), $storage);
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

    public function testThrowsNotFoundExceptionWhenStorageHasNoRequestedDocument() {
        $storage = $this->getMock('Storage_Interface');
        $storage->expects($this->any())->method('getById')
                ->will($this->throwException(new Storage_NotFoundException('123')));

        $this->setExpectedException('Controller_NotFoundException');
        self::c(null, $storage)->GET();
    }

//--------------------------------------------------------------------------------------------------

    private static function c(Request $request = null, Storage_Interface $storage = null) {
        return new Controller(
            $request ?: new Request('/1.gif', null),
            $storage ?: self::aGifStorageStub()
        );
    }

    private static function aGifStorageStub() {
        return Test_Stub::create('Storage_Interface', 'getById', Test_Data::gif1x1());
    }

    private static function binaryRequest() {
        return new Request('/', '0101010111');
    }
}
