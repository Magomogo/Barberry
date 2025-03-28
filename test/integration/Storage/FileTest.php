<?php

namespace Barberry\Storage;

use Barberry\Destination;
use Barberry\Test;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class FileTest extends TestCase
{
    private $storage_path;

    protected function setUp(): void
    {
        $this->storage_path = '/tmp/testStorage-'.random_int(1000, 9999).'/';
        if (!is_dir($this->storage_path)) {
            mkdir($this->storage_path);
        }
    }

    protected function tearDown(): void
    {
        self::rmDirRecursive($this->storage_path);
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

        $destination = new Destination();

        $path = $this->storage_path . $destination->generate($id);
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

    public function testCanDefineContentType(): void{
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );

        assertEquals('image/gif', $this->storage()->getContentTypeById($id)->__toString());
    }

    private function storage(): File
    {
        return new File(new Filesystem(new LocalFilesystemAdapter($this->storage_path)), new Destination());
    }

    private static function rmDirRecursive(string $dir): void {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException("$dir is not a valid directory.");
        }

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                self::rmDirRecursive($path);
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }
}
