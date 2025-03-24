<?php

namespace Barberry;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
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
            new Request('/7yU98sd_1x1.gif'),
        );

        $expectedPath = $this->cache_path . '/7y/U9/8s/7yU98sd/7yU98sd_1x1.gif';

        self::assertStringEqualsFile($expectedPath, Test\Data::gif1x1());
    }

    public function testIsContentSavedInFileSystemInGroupDirectory(): void
    {
        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/adm/7yU98sd_1x1.gif'),
        );

        $expectedPath = $this->cache_path . '/7y/U9/8s/adm/7yU98sd/7yU98sd_1x1.gif';

        self::assertStringEqualsFile($expectedPath, Test\Data::gif1x1());
    }

    public function testInvalidateRemovesCachedContent(): void
    {
        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('/7yU98sd_1x1.gif'),
        );

        $this->cache()->invalidate('7yU98sd');

        self::assertDirectoryDoesNotExist($this->cache_path . '/7y/U9/8s/7yU98sd');
    }

    public function testDirectoryHas775Permissions(): void
    {
        $currentUmask = umask();
        umask(0);

        $this->cache()->save(
            Test\Data::gif1x1(),
            new Request('QWERTY_1x1.gif'),
        );

        $this->assertFileExists($this->cache_path . 'QW/ER/TY/QWERTY/QWERTY_1x1.gif');

        $dirPermissions = fileperms($this->cache_path . 'QW/ER/TY/QWERTY');
        self::assertEquals('775',  decoct( $dirPermissions & 0777 ));

        $permissions = fileperms($this->cache_path . 'QW/ER/TY/QWERTY/QWERTY_1x1.gif');
        self::assertEquals('664',  decoct( $permissions & 0777 ));

        umask($currentUmask);
    }

    private function cache(): Cache
    {
        return new Cache(new Filesystem(new LocalFilesystemAdapter($this->cache_path, new PortableVisibilityConverter(0664, 0600, 0775, 0700, Visibility::PUBLIC))), new Destination());
    }
}
