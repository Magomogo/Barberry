<?php
namespace Barberry;

use Composer\InstalledVersions;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;

class Config
{
    public $directoryStorage;
    private $directoryCache;

    private $applicationPath;
    public FilesystemAdapter $storageAdapter;
    public FilesystemAdapter $cacheAdapter;

    public function __construct(
        $applicationPath = null,
        $configToInclude = null,
        ?FilesystemAdapter $storageAdapter = null,
        ?FilesystemAdapter $cacheAdapter = null
    )
    {
        if (is_null($applicationPath)) {
            $installedPath = InstalledVersions::getRootPackage()['install_path'];
            $applicationPath = $installedPath ?? dirname(__DIR__);
        }
        $this->applicationPath = rtrim($applicationPath, '/');
        $this->setDefaultValues();

        $optionsToOverride = is_file($this->applicationPath . $configToInclude) ?
            include $this->applicationPath . $configToInclude : array();

        foreach ($optionsToOverride as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

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
        $this->directoryCache = $this->applicationPath . '/public/cache/';
        $this->directoryStorage = $this->applicationPath . '/usr/storage/';
    }
}
