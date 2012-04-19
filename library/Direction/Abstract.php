<?php

abstract class Direction_Abstract {
    /**
     * @var null|string
     */
    private $commandString;
    /**
     * @var Plugin_Interface_Converter
     */
    protected $converter;

    public function __construct($commandString = null) {
        $this->commandString = $commandString;
        $this->init();
    }

    abstract function init();

    public function convert($bin) {
        return $this->converter->convert($bin, $this->commandString);
    }
}
