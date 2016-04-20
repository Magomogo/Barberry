<?php
namespace Barberry;

use Barberry\nonlinear;
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
        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/7yU98sd_1x1.gif')
        );

        $expectedPath = $this->cache_path . '/7y/U9/8s/7yU98sd/7yU98sd_1x1.gif';

        $this->assertEquals(file_get_contents($expectedPath), Test\Data::gif1x1());
    }

    public function testIsContentSavedInFileSystemInGroupDirectory() {
        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/adm/7yU98sd_1x1.gif')
        );

        $expectedPath = $this->cache_path . '/7y/U9/8s/adm/7yU98sd/7yU98sd_1x1.gif';

        $this->assertEquals(file_get_contents($expectedPath), Test\Data::gif1x1());
    }

    public function testInvalidateRemovesCachedContent()
    {
        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/7yU98sd_1x1.gif')
        );

        $this->cache()->invalidate('7yU98sd');

        $this->assertFalse(is_dir($this->cache_path . '/7y/U9/8s/7yU98sd'));
    }

    private function cache() {
        return new Cache($this->cache_path);
    }
}
