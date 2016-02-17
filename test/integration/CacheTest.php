<?php
namespace Barberry;

use Barberry\Storage\File\NonLinearDestination;
use Barberry\Test;

class CacheIntegrationTest extends \PHPUnit_Framework_TestCase {

    private $cache_path;

    protected function setUp() {
        $this->cache_path = '/tmp/testCache/';
        @mkdir($this->cache_path);
    }

    protected function tearDown() {
        exec('rm -rf ' . $this->cache_path);
    }

    public function testIsContentSavedInFileSystem() {
        $id = $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/7yU98sd_1x1.gif')
        );

        $path = NonLinearDestination::factory($this->cache_path, $id)->generate();
        $expectedPath = $path . '/7yU98sd/7yU98sd_1x1.gif';

        $this->assertEquals(file_get_contents($expectedPath), Test\Data::gif1x1());
    }

    public function testIsContentSavedInFileSystemInGroupDirectory() {
        $id  = $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/adm/7yU98sd_1x1.gif')
        );

        $path = NonLinearDestination::factory($this->cache_path, $id)->generate();
        $expectedPath = $path . '/adm/7yU98sd/7yU98sd_1x1.gif';

        $this->assertEquals(file_get_contents($expectedPath), Test\Data::gif1x1());
    }

//--------------------------------------------------------------------------------------------------

    private function cache() {
        return new Cache($this->cache_path);
    }
}