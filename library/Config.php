<?php

class Config {

    public $httpHost = 'bin.hostname.domain';

    public $directoryTemp;
    public $directoryStorage;
    public $directoryCache;

//--------------------------------------------------------------------------------------------------

    /**
     * @var Config
     */
    private static $instance;

    public static function get() {
        if (is_null(self::$instance)) {
            self::$instance = new self(
                is_file(APPLICATION_PATH . '/etc/config.php') ?
                        include APPLICATION_PATH . '/etc/config.php' : array()
            );
        }
        return self::$instance;
    }

    public function __construct($optionsToOverride = array()) {
        $this->setDefaultValues();

        foreach ($optionsToOverride as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

//--------------------------------------------------------------------------------------------------

    private function setDefaultValues() {
        $this->directoryCache = APPLICATION_PATH . '/var/cache/';
        $this->directoryTemp = APPLICATION_PATH . '/var/';
        $this->directoryStorage = APPLICATION_PATH . '/usr/storage/';
    }
}
