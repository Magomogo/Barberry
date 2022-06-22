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

    public function testGetReadsStorage(): void
    {
        $storage = $this->createMock('Barberry\\Storage\\StorageInterface');
        $storage
            ->expects($this->once())
            ->method('getById')
            ->willReturn(Utils::streamFor('GIF image'))
            ->with('123asd');

        $storage
            ->expects($this->once())
            ->method('getContentTypeById')
            ->willReturn(ContentType::gif());

        self::controller(new Request('/123asd.gif'), $storage)->get();
    }

    public function testGetReturnsAResponseObject(): void
    {
        self::assertInstanceOf(HttpFoundation\Response::class, self::controller()->get());
    }

    public function testGETResponseContainsCorrectContentType(): void
    {
        $response = self::controller()->get();
        self::assertEquals('image/gif', $response->headers->get('Content-type'));
    }

    public function testSaveIntoCacheOnGetRequest(): void
    {
        $request = new Request('/124234');

        $cacheMock = m::mock(Cache::class);
        $cacheMock
            ->shouldReceive('save')
            ->with(
                m::on(function($stream) {
                    self::assertEquals('GIF image', (string) $stream);
                    return true;
                }),
                $request
            )
            ->once();

        self::controller($request, null, $cacheMock)->get();
    }

    public function testPOSTResponseIsJson(): void
    {
        self::assertEquals(
            'application/json',
            self::controller(self::binaryRequest())->post()->headers->get('Content-type')
        );
    }

    public function testDeleteResponseIsJson(): void
    {
        $response = self::controller()->delete();
        self::assertEquals('application/json', $response->headers->get('Content-type'));
    }

    public function testPostReturnsDocumentInformationAtSavingToStorage(): void
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
            self::controller(self::binaryRequest(), $storageMock)->post()
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
        $controller->post();
    }

    public function testThrowsNullPostValueWhenNoContentPosted(): void
    {
        $this->expectException(NullPostException::class);
        self::controller()->post();
    }

    public function testThrowsNotFoundExceptionWhenUnknownMethodIsCalled(): void
    {
        $this->expectException(NotFoundException::class);
        self::controller()->put();
    }

    public function testThrowsNotFoundExceptionWhenStorageHasNoRequestedDocument(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class);
        $storageMock
            ->shouldReceive('getById')
            ->andThrow(new Storage\NotFoundException('123'));

        $this->expectException(NotFoundException::class);
        self::controller(null, $storageMock)->get();
    }

    public function testSuccessfulPOSTReturns201CreatedCode(): void
    {
        self::assertEquals(
            201,
            self::controller(self::binaryRequest())->post()->getStatusCode()
        );
    }

    public function testDeleteMethodQueriesStorage(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class);
        $storageMock
            ->shouldReceive('delete')
            ->with('124234')
            ->once();

        self::controller(new Request('/124234'), $storageMock)->delete();
    }

    public function testDeleteMethodInvalidatesCache(): void
    {
        $cacheMock = m::mock(Cache::class);
        $cacheMock
            ->shouldReceive('invalidate')
            ->with('124234')
            ->once();

        self::controller(new Request('/124234'), null, $cacheMock)->delete();
    }

    public function testCanDetectOutputContentTypeByContentsOfStorage(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class, [
            'getById' => Utils::streamFor('Content of TXT file'),
            'getContentTypeById' => ContentType::txt()
        ]);

        $response = self::controller(new Request('/11'), $storageMock)->get();
        self::assertEquals('text/plain', $response->headers->get('Content-type'));
    }

    public function testOutputStreamedResponseContent(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class, [
            'getById' => Utils::streamFor('Content of TXT file'),
            'getContentTypeById' => ContentType::txt()
        ]);

        $response = self::controller(new Request('/11'), $storageMock)->get();
        self::assertInstanceOf(HttpFoundation\StreamedResponse::class, $response);

        ob_start();
        $response->send();
        $streamedContent = ob_get_clean();

        self::assertEquals('Content of TXT file', $streamedContent);
    }

    public function testCallConvertForSpecificDirection(): void
    {
        $storageMock = m::mock(Storage\StorageInterface::class, [
            'getById' => Utils::streamFor('Image content'),
            'getContentTypeById' => ContentType::jpeg()
        ]);

        $plugin = m::mock(InterfaceConverter::class);
        $plugin
            ->shouldReceive('convert')
            ->with('Image content')
            ->once();

        $directionFactory = m::mock(Factory::class, ['direction' => $plugin]);

        self::controller(null, $storageMock, null, $directionFactory)->get();
    }

    public function testConversionNotPossibleExceptionCauses404NotFound(): void
    {
        $plugin = m::mock(InterfaceConverter::class);
        $plugin
            ->shouldReceive('convert')
            ->andThrow(ConversionNotPossible::class);

        $directionFactory = m::mock(Factory::class, ['direction' => $plugin]);

        $this->expectException(NotFoundException::class);
        self::controller(null, null, null, $directionFactory)->get();
    }

    private static function controller(
        Request $request = null,
        Storage\StorageInterface $storage = null,
        Cache $cache = null,
        Direction\Factory $directionFactory = null
    ): Controller
    {
        return new Controller(
            $request ?: new Request('/1.gif'),
            $storage ?: m::mock(
                Storage\StorageInterface::class,
                [
                    'getById' => Utils::streamFor('GIF image'),
                    'getContentTypeById' => ContentType::jpeg(),
                    'save' => null,'delete' => null
                ]
            ),
            $cache ?: m::mock(Cache::class, ['save' => true, 'invalidate' => true]),
            $directionFactory ?: m::mock(Factory::class, ['direction' => new Plugin\NullPlugin])
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
