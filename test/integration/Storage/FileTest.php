<?php

namespace Barberry\Storage;

use Barberry\nonlinear;
use Barberry\Test;
use Barberry\fs;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    private $storage_path;

    protected function setUp(): void
    {
        $this->storage_path = '/tmp/testStorage/';
        if (!is_dir($this->storage_path)) {
            mkdir($this->storage_path);
        }
    }

    protected function tearDown(): void
    {
        fs\rmDirRecursive($this->storage_path);
    }

    public function testIsFileSavedInFileSystem(): void
    {
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );
        $content = $this->storage()->getById($id);

        self::assertEquals(Test\Data::gif1x1(), $content);
    }

    public function testIsFileSavedInNonLinearStructure(): void
    {
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );

        $path = $this->storage_path . nonlinear\generateDestination($id);
        self::assertCount(5, array_filter(explode(DIRECTORY_SEPARATOR, $path), function($item) { return !empty($item); }));

        $content = file_get_contents($path . $id);
        self::assertEquals($content, $this->storage()->getById($id));

    }

    public function testReadLinearFile(): void
    {
        $file = tempnam($this->storage_path, '');
        file_put_contents($file, Test\Data::gif1x1());

        $content = $this->storage()->getById(basename($file));
        self::assertEquals(Test\Data::gif1x1(), $content);
    }

    public function testIsFileReturnById(): void
    {
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );
        self::assertEquals($this->storage()->getById($id), Test\Data::gif1x1());
    }

    public function testIsFileDeletedById(): void
    {
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );
        $expectedPath = $this->storage_path.$id;
        $this->storage()->delete($id);

        self::assertFileDoesNotExist($expectedPath);
    }

    public function testNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->storage()->getById('not-existing-id');
    }

    public function testGetByIdTestsForFileExistance(): void
    {
        $this->expectException(NotFoundException::class);
        $this->storage()->getById('/');
    }

    private function storage($path = null): File
    {
        return new File($path ?: $this->storage_path);
    }
}
