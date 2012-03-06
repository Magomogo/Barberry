<?php

class Storage_Exception extends Exception {

    public function __construct($filePath) {
        parent::__construct('File [' . $filePath . '] was not found.');
    }
}
