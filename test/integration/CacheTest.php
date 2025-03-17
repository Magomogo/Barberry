<?php

namespace Barberry;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;

class CacheIntegrationTest extends TestCase
{

    private $cache_path;

    protected function setUp(): void
    {
        $this->cache_path = '/tmp/testCache/';
        @mkdir($this->cache_path);
    }

    protected function tearDown(): void
    {
        exec('rm -rf ' . $this->cache_path);
    }

    public function testIsContentSavedInFileSystem(): void
    {
        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/7yU98sd_1x1.gif')
        );

        $expectedPath = $this->cache_path . '/7y/U9/8s/7yU98sd/7yU98sd_1x1.gif';

        self::assertStringEqualsFile($expectedPath, Test\Data::gif1x1());
    }

    public function testIsContentSavedInFileSystemInGroupDirectory(): void
    {
        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/adm/7yU98sd_1x1.gif')
        );

        $expectedPath = $this->cache_path . '/7y/U9/8s/adm/7yU98sd/7yU98sd_1x1.gif';

        self::assertStringEqualsFile($expectedPath, Test\Data::gif1x1());
    }

    public function testInvalidateRemovesCachedContent(): void
    {
        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/7yU98sd_1x1.gif')
        );

        $this->cache()->invalidate('7yU98sd');

        self::assertDirectoryDoesNotExist($this->cache_path . '/7y/U9/8s/7yU98sd');
    }

    private function cache(): Cache
    {
        return new Cache(new Filesystem(new LocalFilesystemAdapter($this->cache_path)), new Destination());
    }
}
