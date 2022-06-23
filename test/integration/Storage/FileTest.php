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

    public function testIsFileSavedInFileSystem()
    {
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );
        $content = $this->storage()->getById($id);

        $this->assertEquals(Test\Data::gif1x1(), $content);
    }

    public function testIsFileSavedInNonLinearStructure()
    {
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );

        $path = $this->storage_path . nonlinear\generateDestination($id);
        $this->assertCount(5, array_filter(explode(DIRECTORY_SEPARATOR, $path), function($item) { return !empty($item); }));

        $content = file_get_contents($path . $id);
        $this->assertEquals($content, $this->storage()->getById($id));

    }

    public function testReadLinearFile()
    {
        $file = tempnam($this->storage_path, '');
        file_put_contents($file, Test\Data::gif1x1());

        $content = $this->storage()->getById(basename($file));
        $this->assertEquals(Test\Data::gif1x1(), $content);
    }

    public function testIsFileReturnById()
    {
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );
        $this->assertEquals($this->storage()->getById($id), Test\Data::gif1x1());
    }

    public function testIsFileDeletedById()
    {
        $id = $this->storage()->save(
            new UploadedFile(Utils::tryFopen(__DIR__ . '/../data/1x1.gif', 'r'), 43, UPLOAD_ERR_OK)
        );
        $expectedPath = $this->storage_path.$id;
        $this->storage()->delete($id);
        $this->assertFalse(file_exists($expectedPath));
    }

    public function testNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $this->storage()->getById('not-existing-id');
    }

    public function testGetByIdTestsForFileExistance()
    {
        $this->expectException(NotFoundException::class);
        $this->storage()->getById('/');
    }

    private function storage($path = null)
    {
        return new File($path ?: $this->storage_path);
    }
}
