<?php

class Direction_Abstract {
    /**
     * @var Plugin_Interface_Converter
     */
    protected $converter;

    public function convert($bin) {
        return $this->converter->convert($bin);
    }
}
