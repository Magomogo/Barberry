<?php

class Controller_NotImplementedException extends Exception {

    public function __construct($msg) {
        parent::__construct($msg);
    }
}
