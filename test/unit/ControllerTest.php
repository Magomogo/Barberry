<?php

namespace Barberry;

use Barberry\Storage;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private static $filesystem;

    public function setUp()
    {
        parent::setUp();

        self::$filesystem = vfsStream::setup('root', null, [
            'tmp' => [
                'aD6gsl' => "Column1\tColumn2\tColumn3",
                'test.odt' => dechex(0)
            ]
        ]);
    }

    public function testDataType()
    {
        $this->assertInstanceOf('Barberry\Controller\ControllerInterface', self::controller());
    }

    public function testGETReadsStorage()
    {
        $storage = $this->createMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->once())
                ->method('getById')
                ->willReturn(Test\Data::gif1x1())
                ->with('123asd');

        self::controller(new Request('/123asd.gif'), $storage)->GET();
    }

    public function testGETReturnsAResponseObject()
    {
        $this->assertInstanceOf('Barberry\\Response', self::controller()->GET());
    }

    public function testGETResponseContainsCorrectContentType()
    {
        $response = self::controller()->GET();
        $this->assertEquals(ContentType::gif(), $response->contentType);
    }

    public function testPOSTResponseIsJson()
    {
        $this->assertEquals(
            ContentType::json(),
            self::controller(self::binaryRequest())->POST()->contentType
        );
    }

    public function testDELETEResponseIsJson()
    {
        $response = self::controller()->DELETE();
        $this->assertEquals(ContentType::json(), $response->contentType);
    }

    public function testPOSTReturnsDocumentInformationAtSavingToStorage()
    {
        $storage = $this->createMock('Barberry\\Storage\\StorageInterface');
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
                        'md5' => '53cd331224e92796bacd68433b111fca'
                    )
                ),
                201
            ),
            self::controller(self::binaryRequest(), $storage)->POST()
        );
    }

    public function testSavesPostedContentToTheStorage()
    {
        $storage = $this->createMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->once())->method('save')->with('0101010111');
        $controller = self::controller(self::binaryRequest(), $storage);
        $controller->POST();
    }

    public function testThrowsNullPostValueWhenNoContentPosted()
    {
        $this->expectException('Barberry\\Controller\\NullPostException');
        self::controller()->POST();
    }

    public function testThrowsNotFoundExceptionWhenUnknownMethodIsCalled()
    {
        $this->expectException('Barberry\\Controller\\NotFoundException');
        self::controller()->PUT();
    }

    public function testThrowsNotFoundExceptionWhenStorageHasNoRequestedDocument()
    {
        $storage = $this->createMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->any())->method('getById')
                ->will($this->throwException(new Storage\NotFoundException('123')));

        $this->expectException('Barberry\\Controller\\NotFoundException');
        self::controller(null, $storage)->GET();
    }

    public function testSuccessfullPOSTReturns201CreatedCode()
    {
        $this->assertEquals(201, self::controller(self::binaryRequest())->POST()->code);
    }

    public function testPOSTOfUnknownContentTypeReturns501NotImplemented()
    {
        $this->expectException('Barberry\\Controller\\NotImplementedException');

        $postedFile = new PostedFile(dechex(0), self::$filesystem->url() . '/tmp/test.odt', 'test.odt');
        self::controller(new Request('/', $postedFile))->POST();
    }

    public function testDeleteMethodQueriesStorage()
    {
        $storage = $this->createMock('Barberry\\Storage\\StorageInterface');
        $storage->expects($this->once())->method('delete')->with('124234');

        self::controller(new Request('/124234'), $storage)->DELETE();
    }

    public function testCanDetectOutputContentTypeByContentsOfStorage()
    {
        $this->assertEquals(
            new Response(ContentType::txt(), '123'),
            self::controller(
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

        $this->expectException('Barberry\\Controller\\NotFoundException');
        self::controller(null, null, $directionFactory)->GET();
    }

    private static function controller(
        Request $request = null,
        Storage\StorageInterface $storage = null,
        Direction\Factory $directionFactory = null
    ) {
        return new Controller(
            $request ?: new Request('/1.gif'),
            $storage ?: m::mock(
                'Barberry\\Storage\\StorageInterface',
                array('getById' => Test\Data::gif1x1(), 'save' => null, 'delete' => null)
            ),
            $directionFactory ?: m::mock('Barberry\\Direction\\Factory', array('direction' => new Plugin\NullPlugin))
        );
    }

    private static function binaryRequest()
    {
        return new Request('/', new PostedFile('0101010111', self::$filesystem->url() . '/tmp/aD6gsl', 'File.txt'));
    }
}
