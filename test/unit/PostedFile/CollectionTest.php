<?php

namespace Barberry\PostedFile;

use Barberry\PostedFile;
use Barberry\Test;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    private static $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        self::$filesystem = vfsStream::setup('root', null, [
            'tmp' => [
                '1254432ks3' => "Column1\tColumn2\tColumn3",
                '1254432ks1' => 'GIF binary'
            ]
        ]);
    }

    public function testImplementsIteratorAndArrayAccessInterfaces(): void
    {
        $collection = new Collection([]);
        self::assertInstanceOf(\Iterator::class, $collection);
        self::assertInstanceOf(\ArrayAccess::class, $collection);
    }

    public function testCanBeIteratedThrowFiles(): void
    {
        $collection = $this->partiallyMockedCollection(
            [
                'file' => self::goodFileInPhpFilesArray(),
                'image' => self::additionalFileInPhpFilesArray()
            ]
        );

        $result = [];
        foreach ($collection as $key => $value) {
            $result[$key] = $value;
        }

        self::assertCount(2, $result);
        self::assertEquals('Name of a file.txt', $result['file']->uploadedFile->getClientFilename());
        self::assertEquals('another.gif', $result['image']->uploadedFile->getClientFilename());
    }

    public function testReturnsPostedFileCreatedFromUploadedFilesPhpSpec(): void
    {
        $collection = $this->partiallyMockedCollection();
        self::assertInstanceOf(PostedFile::class, $collection['file']);
    }

    public function testThrowsExceptionOnSetWrongPostedFile(): void
    {
        $this->expectException(CollectionException::class);
        $collection = $this->partiallyMockedCollection();
        $collection['file'] = new \stdClass();
    }

    public function testCanSetPostedFile(): void
    {
        $collection = $this->partiallyMockedCollection();
        $collection['file'] = new PostedFile(
            new UploadedFile(Utils::streamFor('xxx'), 10, UPLOAD_ERR_OK, 'test.txt'),
            '/tmp/asD6yhq'
        );

        self::assertEquals('test.txt', $collection['file']->uploadedFile->getClientFilename());
    }

    public function testCanAddNewPostedFile(): void
    {
        $collection = $this->partiallyMockedCollection();
        $collection['image'] = new PostedFile(
            new UploadedFile('ssdgsdfg', 10, UPLOAD_ERR_OK, 'test.txt'),
            '/tmp/asD6yhq'
        );

        $collection->rewind();
        $collection->next();

        self::assertEquals('test.txt', $collection->current()->uploadedFile->getClientFilename());
    }

    public function testCanUnshiftPostedFileToTheBeginning(): void
    {
        $collection = $this->partiallyMockedCollection();
        $collection->unshift('image', new PostedFile(
            new UploadedFile('ssdgsdfg', 10, UPLOAD_ERR_OK, 'test.txt'),
            '/tmp/asD6yhq'
        ));

        $collection->rewind();
        self::assertEquals('test.txt', $collection->current()->uploadedFile->getClientFilename());

        $collection->next();
        self::assertEquals('Name of a file.txt', $collection->current()->uploadedFile->getClientFilename());
    }

    public function testIgnoresBadFiles(): void
    {
        $collection = $this->partiallyMockedCollection(['file' => self::badFileInPhpFilesArray()]);

        foreach ($collection as $v) {
            self::fail('Should not contain any files');
        }

        self::assertNull($collection[0]);
    }

    public function testCanBeCreatedWithPostedFilesInConstructor(): void
    {
        $collection = new Collection([
            'file' => new PostedFile(
                new UploadedFile(Utils::streamFor('gif content'), 10, UPLOAD_ERR_OK, 'test.gif'),
                '/tmp/asD6yhq'
            )
        ]);
        $this->assertEquals('gif content', $collection['file']->uploadedFile->getStream()->getContents());
    }

    private static function goodFileInPhpFilesArray(): array
    {
        return [
            'size' => 1234,
            'tmp_name' => self::$filesystem->url() . '/tmp/1254432ks3',
            'error' => UPLOAD_ERR_OK,
            'name' => 'Name of a file.txt'
        ];
    }

    private static function additionalFileInPhpFilesArray(): array
    {
        return [
            'size' => 123,
            'tmp_name' => self::$filesystem->url() . '/tmp/1254432ks1',
            'error' => UPLOAD_ERR_OK,
            'name' => 'another.gif'
        ];
    }

    private static function badFileInPhpFilesArray()
    {
        return [
            'size' => 0,
            'error' => UPLOAD_ERR_CANT_WRITE
        ];
    }

    private function partiallyMockedEmptyCollection(array $specs = null)
    {
        if ($specs === null) {
            $specs = ['file' => self::goodFileInPhpFilesArray()];
        }

        return $this->getMockBuilder('Barberry\\PostedFile\\Collection')
            ->setMethods(['readTempFile'])
            ->enableOriginalConstructor()
            ->setConstructorArgs([$specs])
            ->getMock();

    }

    private function partiallyMockedCollection(array $specs = null, $readFile = null)
    {
        $partialMock = $this->partiallyMockedEmptyCollection($specs);
        $partialMock
            ->expects($this->any())
            ->method('readTempFile')
            ->willReturn($readFile ?: Test\Data::gif1x1());

        return $partialMock;
    }

}
