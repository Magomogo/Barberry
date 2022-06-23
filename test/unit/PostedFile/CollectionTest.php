<?php

namespace Barberry\PostedFile;

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

    public function testImplementsIteratorAndArrayAccessInterfaces()
    {
        $collection = new Collection([]);
        $this->assertInstanceOf(\Iterator::class, $collection);
        $this->assertInstanceOf(\ArrayAccess::class, $collection);
    }

    public function testCanBeIteratedThrowFiles()
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

        $this->assertEquals(2, count($result));
        $this->assertEquals('Name of a file.txt', $result['file']->uploadedFile->getClientFilename());
        $this->assertEquals('another.gif', $result['image']->uploadedFile->getClientFilename());
    }

    public function testReturnsPostedFileCreatedFromUploadedFilesPhpSpec()
    {
        $collection = $this->partiallyMockedCollection();
        $this->assertInstanceOf('Barberry\\PostedFile', $collection['file']);
    }

    public function testThrowsExceptionOnSetWrongPostedFile()
    {
        $this->expectException(CollectionException::class);
        $collection = $this->partiallyMockedCollection();
        $collection['file'] = new \stdClass();
    }

    public function testCanSetPostedFile()
    {
        $collection = $this->partiallyMockedCollection();
        $collection['file'] = new \Barberry\PostedFile(
            new UploadedFile(Utils::streamFor('xxx'), 10, UPLOAD_ERR_OK, 'test.txt'),
            '/tmp/asD6yhq'
        );

        $this->assertEquals('test.txt', $collection['file']->uploadedFile->getClientFilename());
    }

    public function testCanAddNewPostedFile()
    {
        $collection = $this->partiallyMockedCollection();
        $collection['image'] = new \Barberry\PostedFile(
            new UploadedFile('ssdgsdfg', 10, UPLOAD_ERR_OK, 'test.txt'),
            '/tmp/asD6yhq'
        );

        $collection->rewind();
        $collection->next();

        $this->assertEquals('test.txt', $collection->current()->uploadedFile->getClientFilename());
    }

    public function testCanUnshiftPostedFileToTheBeginning() 
    {
        $collection = $this->partiallyMockedCollection();
        $collection->unshift('image', new \Barberry\PostedFile(
            new UploadedFile('ssdgsdfg', 10, UPLOAD_ERR_OK, 'test.txt'),
            '/tmp/asD6yhq'
        ));

        $collection->rewind();
        $this->assertEquals('test.txt', $collection->current()->uploadedFile->getClientFilename());

        $collection->next();
        $this->assertEquals('Name of a file.txt', $collection->current()->uploadedFile->getClientFilename());
    }

    public function testIgnoresBadFiles()
    {
        $collection = $this->partiallyMockedCollection(['file' => self::badFileInPhpFilesArray()]);

        foreach ($collection as $v) {
            $this->fail('Should not contain any files');
        }

        self::assertNull($collection[0]);
    }

    public function testCanBeCreatedWithPostedFilesInConstructor()
    {
        $collection = new Collection([
            'file' => new \Barberry\PostedFile(
                new UploadedFile(Utils::streamFor('gif content'), 10, UPLOAD_ERR_OK, 'test.gif'),
                '/tmp/asD6yhq'
            )
        ]);
        $this->assertEquals('gif content', $collection['file']->uploadedFile->getStream()->getContents());
    }

    private static function goodFileInPhpFilesArray()
    {
        return [
            'size' => 1234,
            'tmp_name' => self::$filesystem->url() . '/tmp/1254432ks3',
            'error' => UPLOAD_ERR_OK,
            'name' => 'Name of a file.txt'
        ];
    }

    private static function additionalFileInPhpFilesArray()
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
            $specs = array('file' => self::goodFileInPhpFilesArray());
        }

        return $this->getMockBuilder('Barberry\\PostedFile\\Collection')
            ->setMethods(['readTempFile'])
            ->enableOriginalConstructor()
            ->setConstructorArgs(array($specs))
            ->getMock();

    }

    private function partiallyMockedCollection(array $specs = null, $readFile = null)
    {
        $partialMock = $this->partiallyMockedEmptyCollection($specs);
        $partialMock
            ->expects($this->any())
            ->method('readTempFile')
            ->will($this->returnValue($readFile ?: Test\Data::gif1x1()));

        return $partialMock;
    }

}
