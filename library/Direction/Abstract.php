<?php

abstract class Direction_Abstract {
    /**
     * @var null|Plugin_Interface_Command
     */
    protected $command;

    /**
     * @var Plugin_Interface_Converter
     */
    protected $converter;

    public function __construct($commandString = null) {
        $this->init($commandString);
    }

    abstract protected function init($commandString = null);

    public function convert($bin) {
        return $this->converter->convert($bin, $this->command);
    }
}
