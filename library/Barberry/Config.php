<?php
namespace Barberry;

use Composer\InstalledVersions;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;

class Config
{
    private string $directoryStorage;
    private string $directoryCache;

    private string $applicationPath;
    public FilesystemAdapter $storageAdapter;
    public FilesystemAdapter $cacheAdapter;
    private int $depth = 3;

    /**
     * When you pass null, by default will be used ../public/cache/ and ../usr/storage/ locations
     *
     * @param FilesystemAdapter|null $storageAdapter
     * @param FilesystemAdapter|null $cacheAdapter
     */
    public function __construct(
        ?FilesystemAdapter $storageAdapter = null,
        ?FilesystemAdapter $cacheAdapter = null
    )
    {
        $this->setDefaultValues();

        if (is_null($storageAdapter)) {
            $storageAdapter = new LocalFilesystemAdapter($this->directoryStorage);
        }
        if (is_null($cacheAdapter)) {
            $cacheAdapter = new LocalFilesystemAdapter($this->directoryCache);
        }
        $this->storageAdapter = $storageAdapter;
        $this->cacheAdapter = $cacheAdapter;
    }

    private function setDefaultValues(): void
    {
        $installedPath = InstalledVersions::getRootPackage()['install_path'];
        $this->applicationPath = $installedPath ?? dirname(__DIR__);
        $this->applicationPath = rtrim($this->applicationPath, '/');

        $this->directoryCache = $this->applicationPath . '/public/cache/';
        $this->directoryStorage = $this->applicationPath . '/usr/storage/';
    }

    public function setDepth(int $depth): void
    {
        $this->depth = $depth;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }
}
