<?php

class Controller_NullPostException extends Exception {

    public function __construct() {
        parent::__construct('No data POSTed');
    }
}
