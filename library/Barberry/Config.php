<?php
namespace Barberry;

class Config
{
    public $httpHost = 'bin.hostname.domain';

    public $directoryTemp;
    public $directoryStorage;
    public $directoryCache;
    public $directoryEnabledDirection;

    public $applicationPath;

    public function __construct($applicationPath, $optionsToOverride = array())
    {
        $this->applicationPath = rtrim($applicationPath, '/');
        $this->setDefaultValues();

        foreach ($optionsToOverride as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    private function setDefaultValues()
    {
        $this->directoryCache = $this->applicationPath . '/public/cache/';
        $this->directoryTemp = $this->applicationPath . '/var/';
        $this->directoryStorage = $this->applicationPath . '/usr/storage/';
        $this->directoryEnabledDirection = $this->applicationPath . '/barberry-directions/';
    }
}
