<?php
namespace Barberry;

class Config
{
    public $directoryStorage;
    public $directoryCache;
    public $directoryMonitors;

    public $applicationPath;

    public function __construct($applicationPath, $configToInclude = null)
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
        $this->directoryMonitors = $this->applicationPath . '/barberry-monitors/';
    }
}
