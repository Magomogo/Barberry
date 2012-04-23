<?php

class Plugin_Pdf_Command implements Plugin_Interface_Command {

    private $width;

    public function __construct($commandString) {
        $width = is_numeric($commandString) ? intval($commandString) : 800;
        $this->width = min(2000, max(10, $width));
    }

    public function width() {
        return $this->width;
    }
}
