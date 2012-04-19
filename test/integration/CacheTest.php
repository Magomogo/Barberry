<?php

class CacheTest extends PHPUnit_Framework_TestCase {

    private $cache_path;

    protected function setUp() {
        $this->cache_path = Config::get()->directoryTemp . 'testCache/';
        mkdir($this->cache_path);
    }

    protected function tearDown() {
        self::rmDirRecursive($this->cache_path);
    }

    public function testIsContentSavedInFileSystem() {
        $requested_uri = '/7yU98sd/1x1.gif';

        $this->cache()->save(
            Test_Data::gif1x1(),
            $requested_uri
        );

        $expectedPath = $this->cache_path.ltrim($requested_uri, '/');

        $this->assertEquals(file_get_contents($expectedPath), Test_Data::gif1x1());
    }

    public function testIsContentSavedInFileSystemInGroupDirectory() {
        $requested_uri = '/admin/7yU98sd/1x1.gif';

        $this->cache()->save(
            Test_Data::gif1x1(),
            $requested_uri
        );

        $expectedPath = $this->cache_path.ltrim($requested_uri, '/');

        $this->assertEquals(file_get_contents($expectedPath), Test_Data::gif1x1());
    }

//--------------------------------------------------------------------------------------------------

    private function cache() {
        return new Cache($this->cache_path);
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