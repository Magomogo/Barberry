<?php
namespace Barberry;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;

class Config
{
    public $directoryStorage;
    public $directoryCache;

    public $applicationPath;
    public FilesystemAdapter $storageAdapter;
    public FilesystemAdapter $cacheAdapter;

    public function __construct(
        $applicationPath,
        $configToInclude = null,
        ?FilesystemAdapter $storageAdapter = null,
        ?FilesystemAdapter $cacheAdapter = null
    )
    {
        $this->applicationPath = rtrim($applicationPath, '/');
        $this->setDefaultValues();

        $optionsToOverride = is_file($this->applicationPath . $configToInclude) ?
            include $this->applicationPath . $configToInclude : array();

        foreach ($optionsToOverride as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    private function setDefaultValues()
    {
        $this->directoryCache = $this->applicationPath . '/public/cache/';
        $this->directoryStorage = $this->applicationPath . '/usr/storage/';
        $this->cacheAdapter = new LocalFilesystemAdapter($this->directoryCache);
        $this->storageAdapter = new LocalFilesystemAdapter($this->directoryStorage);
    }
}
