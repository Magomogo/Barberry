<?php

class Storage_FileTest extends PHPUnit_Framework_TestCase {

    private $storage_path;

    protected function setUp() {
        $this->storage_path = Config::get()->directoryTemp . 'testStorage/';
        mkdir($this->storage_path);
    }

    protected function tearDown() {
        self::rmDirRecursive($this->storage_path);
    }

    public function testIsFileSavedInFileSystem() {
        $id = $this->storage()->save(Test_Data::gif1x1());
        $expectedPath = $this->storage_path.$id;
        $this->assertEquals(file_get_contents($expectedPath), Test_Data::gif1x1());
    }

    public function testIsFileReturnById() {
        $id = $this->storage()->save(Test_Data::gif1x1());
        $this->assertEquals($this->storage()->getById($id), Test_Data::gif1x1());
    }

    public function testIsFileDeletedById() {
        $id = $this->storage()->save(Test_Data::gif1x1());
        $expectedPath = $this->storage_path.$id;
        $this->storage()->delete($id);
        $this->assertFalse(file_exists($expectedPath));
    }

//--------------------------------------------------------------------------------------------------

    private function storage() {
        return new Storage_File($this->storage_path);
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