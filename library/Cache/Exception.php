<?php

class Cache_Exception extends Exception {

    public function __construct($path) {
        parent::__construct('Cache path [' . $path . '] was not created.');
    }
}
