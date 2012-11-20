<?php
namespace Barberry;
use Barberry\Storage;
use Mockery as m;

class ControllerTest extends \PHPUnit_Framework_TestCase {

    public function testDataType() {
        $this->assertInstanceOf('Barberry\Controller\ControllerInterface', self::c());
    }

    public function testGETReadsStorage() {
        $storage = $this->getMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->once())
                ->method('getById')
                ->will($this->returnValue(Test\Data::gif1x1()))
                ->with('123asd');
        $this->c(new Request('/123asd.gif'), $storage)->GET();
    }

    public function testGETReturnsAResponseObject() {
        $this->assertInstanceOf('Barberry\\Response', self::c()->GET());
    }

    public function testGETResponseContainsCorrectContentType() {
        $response = $this->c()->GET();
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

    public function testPOSTReturnsDocumentInformationAtSavingToStorage() {
        $storage = $this->getMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->once())->method('save')->will($this->returnValue('12345xz'));

        $this->assertEquals(
            new Response(
                ContentType::json(), json_encode(
                    array(
                        'id'=>'12345xz',
                        'contentType' => 'text/plain',
                        'ext' => 'txt',
                        'length' => 10,
                        'filename' => 'File.txt',
                    )
                ),
                201
            ),
            $this->c(self::binaryRequest(), $storage)->POST()
        );
    }

    public function testSavesPostedContentToTheStorage() {
        $storage = $this->getMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->once())->method('save')->with('0101010111');
        $controller = $this->c(self::binaryRequest(), $storage);
        $controller->POST();
    }

    public function testThrowsNullPostValueWhenNoContentPosted() {
        $this->setExpectedException('Barberry\\Controller\\NullPostException');
        self::c()->POST();
    }

    public function testThrowsNotFoundExceptionWhenUnknownMethodIsCalled() {
        $this->setExpectedException('Barberry\\Controller\\NotFoundException');
        self::c()->PUT();
    }

    public function testThrowsNotFoundExceptionWhenStorageHasNoRequestedDocument() {
        $storage = $this->getMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->any())->method('getById')
                ->will($this->throwException(new Storage\NotFoundException('123')));

        $this->setExpectedException('Barberry\\Controller\\NotFoundException');
        self::c(null, $storage)->GET();
    }

    public function testSuccessfullPOSTReturns201CreatedCode() {
        $this->assertEquals(201, self::c(self::binaryRequest())->POST()->code);
    }

    public function testPOSTOfUnknownContentTypeReturns501NotImplemented() {
        $this->setExpectedException('Barberry\\Controller\\NotImplementedException');
        self::c(new Request('/', array('content' => dechex(0))))->POST();
    }

    public function testDeleteMethodQueriesStorage() {
        $storage = $this->getMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->once())->method('delete')->with('124234');

        self::c(new Request('/124234'), $storage)->DELETE();
    }

    public function testCanDetectOutputContentTypeByContentsOfStorage() {
        $this->assertEquals(
            new Response(ContentType::txt(), '123'),
            self::c(
                new Request('/11'),
                m::mock('Barberry\\Storage\\StorageInterface', array('getById' => '123'))
            )->GET()
        );
    }

    public function testConversionNotPossibleExceptionCauses404NotFound()
    {
        $plugin = m::mock('Barberry\\Plugin\\InterfaceConverter');
        $plugin->shouldReceive('convert')->andThrow('Barberry\\Exception\\ConversionNotPossible');

        $directionFactory = m::mock(
            'Barberry\\Direction\\Factory',
            array('direction' => $plugin)
        );


        $this->setExpectedException('Barberry\\Controller\\NotFoundException');
        self::c(null, null, $directionFactory)->GET();
}

//--------------------------------------------------------------------------------------------------

    private static function c(Request $request = null, Storage\StorageInterface $storage = null,
        Direction\Factory $directionFactory = null) {
        return new Controller(
            $request ?: new Request('/1.gif'),
            $storage ?: self::aGifStorageStub(),
            $directionFactory ?: m::mock('Barberry\\Direction\\Factory', array('direction' => new Plugin\Null))
        );
    }

    private static function aGifStorageStub() {
        return Test\Stub::create('Barberry\\Storage\\StorageInterface', 'getById', Test\Data::gif1x1());
    }

    private static function binaryRequest() {
        return new Request('/', array('content' => '0101010111', 'filename' => 'File.txt'));
    }
}
