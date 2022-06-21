<?php

namespace Barberry;

use Barberry\Controller\ControllerInterface;
use Barberry\Controller\NotFoundException;
use Barberry\Controller\NullPostException;
use Barberry\Direction\Factory;
use Barberry\Exception\ConversionNotPossible;
use Barberry\Plugin\InterfaceConverter;
use Barberry\Storage;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\HttpFoundation;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private static $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        self::$filesystem = vfsStream::setup('root', null, [
            'tmp' => [
                'aD6gsl' => "Column1\tColumn2\tColumn3",
                'test.odt' => dechex(0)
            ]
        ]);
    }

    public function testDataType(): void
    {
        self::assertInstanceOf(ControllerInterface::class, self::controller());
    }

    public function testGETReadsStorage(): void
    {
        $storage = $this->createMock('Barberry\\Storage\\StorageInterface');
        $storage
            ->expects($this->once())
            ->method('getById')
            ->willReturn(Test\Data::gif1x1())
            ->with('123asd');

        $storage
            ->expects($this->once())
            ->method('getContentTypeById')
            ->willReturn(ContentType::gif());

        self::controller(new Request('/123asd.gif'), $storage)->GET();
    }

    public function testGETReturnsAResponseObject(): void
    {
        self::assertInstanceOf(HttpFoundation\Response::class, self::controller()->GET());
    }

    public function testGETResponseContainsCorrectContentType(): void
    {
        $response = self::controller()->GET();
        self::assertEquals('image/gif', $response->headers->get('Content-type'));
    }

    public function testPOSTResponseIsJson(): void
    {
        self::assertEquals(
            'application/json',
            self::controller(self::binaryRequest())->POST()->headers->get('Content-type')
        );
    }

    public function testDELETEResponseIsJson(): void
    {
        $response = self::controller()->DELETE();
        self::assertEquals('application/json', $response->headers->get('Content-type'));
    }

    public function testPOSTReturnsDocumentInformationAtSavingToStorage(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class);
        $storageMock
            ->shouldReceive('save')
            ->andReturn('12345xz')
            ->once();

        $this->assertEquals(
            (new HttpFoundation\JsonResponse(
                [
                    'id'=>'12345xz',
                    'contentType' => 'text/plain',
                    'ext' => 'txt',
                    'length' => 10,
                    'filename' => 'File.txt',
                    'md5' => 'ac0f570b0a82fc7d0b08f5098acc1a33'
                ],
                201
            ))->setProtocolVersion('1.1'),
            self::controller(self::binaryRequest(), $storageMock)->POST()
        );
    }

    public function testSavesPostedContentToTheStorage(): void
    {
        $storage = m::mock(Storage\StorageInterface::class);
        $storage
            ->shouldReceive('save')
            ->with(m::on(function ($file) {
                return $file instanceof UploadedFile && $file->getStream()->getContents() === '0101010111';
            }))
            ->once();

        $controller = self::controller(self::binaryRequest(), $storage);
        $controller->POST();
    }

    public function testThrowsNullPostValueWhenNoContentPosted(): void
    {
        $this->expectException(NullPostException::class);
        self::controller()->POST();
    }

    public function testThrowsNotFoundExceptionWhenUnknownMethodIsCalled(): void
    {
        $this->expectException(NotFoundException::class);
        self::controller()->PUT();
    }

    public function testThrowsNotFoundExceptionWhenStorageHasNoRequestedDocument(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class);
        $storageMock
            ->shouldReceive('getById')
            ->andThrow(new Storage\NotFoundException('123'));

        $this->expectException(NotFoundException::class);
        self::controller(null, $storageMock)->GET();
    }

    public function testSuccessfulPOSTReturns201CreatedCode(): void
    {
        self::assertEquals(
            201,
            self::controller(self::binaryRequest())->POST()->getStatusCode()
        );
    }

    public function testDeleteMethodQueriesStorage(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class);
        $storageMock
            ->shouldReceive('delete')
            ->with('124234')
            ->once();

        self::controller(new Request('/124234'), $storageMock)->DELETE();
    }

    public function testCanDetectOutputContentTypeByContentsOfStorage(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class, [
            'getById' => '123',
            'getContentTypeById' => ContentType::txt()
        ]);

        $expectedResponse = (new HttpFoundation\Response(
            '123',
            200,
            ['Content-type' => ContentType::txt()])
        )->setProtocolVersion('1.1');

        self::assertEquals(
            $expectedResponse,
            self::controller(new Request('/11'), $storageMock)->GET()
        );
    }

    public function testConversionNotPossibleExceptionCauses404NotFound(): void
    {
        $plugin = m::mock(InterfaceConverter::class);
        $plugin
            ->shouldReceive('convert')
            ->andThrow(ConversionNotPossible::class);

        $directionFactory = m::mock(Factory::class, ['direction' => $plugin]);

        $this->expectException(NotFoundException::class);
        self::controller(null, null, $directionFactory)->GET();
    }

    private static function controller(
        Request $request = null,
        Storage\StorageInterface $storage = null,
        Direction\Factory $directionFactory = null
    ): Controller
    {
        return new Controller(
            $request ?: new Request('/1.gif'),
            $storage ?: m::mock(
                'Barberry\\Storage\\StorageInterface',
                [
                    'getById' => Test\Data::gif1x1(),
                    'getContentTypeById' => ContentType::jpeg(),
                    'save' => null,'delete' => null
                ]
            ),
            $directionFactory ?: m::mock('Barberry\\Direction\\Factory', array('direction' => new Plugin\NullPlugin))
        );
    }

    private static function binaryRequest(): Request
    {
        return new Request(
            '/',
            new PostedFile(
                new UploadedFile(Utils::streamFor('0101010111'), 10, UPLOAD_ERR_OK, 'File.txt'),
                self::$filesystem->url() . '/tmp/aD6gsl'
            )
        );
    }
}
