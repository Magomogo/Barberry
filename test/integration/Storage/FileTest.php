<?php
namespace Barberry\Storage;

use Barberry\nonlinear;
use Barberry\Test;

class FileTest extends \PHPUnit_Framework_TestCase {

    private $storage_path;

    protected function setUp() {
        $this->storage_path = '/tmp/testStorage/';
        if (!is_dir($this->storage_path)) {
            mkdir($this->storage_path);
        }
    }

    protected function tearDown() {
        self::rmDirRecursive($this->storage_path);
    }

    public function testIsFileSavedInFileSystem() {
        $id = $this->storage()->save(Test\Data::gif1x1());
        $content = $this->storage()->getById($id);
        $this->assertEquals(Test\Data::gif1x1(), $content);
    }

    public function testIsFileSavedInNonLinearStructure() {
        $id = $this->storage()->save(Test\Data::gif1x1());
        $path = $this->storage_path . nonlinear\generateDestination($id);
        $this->assertCount(5, array_filter(explode(DIRECTORY_SEPARATOR, $path), function($item) { return !empty($item); }));

        $content = file_get_contents($path . $id);
        $this->assertEquals($content, $this->storage()->getById($id));

    }

    public function testReadLinearFile() {
        $file = tempnam($this->storage_path, '');
        file_put_contents($file, Test\Data::gif1x1());

        $content = $this->storage()->getById(basename($file));
        $this->assertEquals(Test\Data::gif1x1(), $content);
    }

    public function testIsFileReturnById() {
        $id = $this->storage()->save(Test\Data::gif1x1());
        $this->assertEquals($this->storage()->getById($id), Test\Data::gif1x1());
    }

    public function testIsFileDeletedById() {
        $id = $this->storage()->save(Test\Data::gif1x1());
        $expectedPath = $this->storage_path.$id;
        $this->storage()->delete($id);
        $this->assertFalse(file_exists($expectedPath));
    }

    public function testNotFoundException() {
        $this->setExpectedException('Barberry\\Storage\\NotFoundException');
        $this->storage()->getById('not-existing-id');
    }

    public function testGetByIdTestsForFileExistance() {
        $this->setExpectedException('Barberry\\Storage\\NotFoundException');
        $this->storage()->getById('/');
    }

    private function storage($path = null) {
        return new File($path ?: $this->storage_path);
    }

    private static function rmDirRecursive($dir) {
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') continue;
            if (!self::rmDirRecursive($dir . '/' . $file)) {
                chmod($dir . '/' . $file, 0777);
                if (!self::rmDirRecursive($dir . '/' . $file)) return false;
            };
        }
        return rmdir($dir);
    }
}
