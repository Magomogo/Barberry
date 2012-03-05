<?php

class Controller_NotFoundException extends Exception {

    public function __construct() {
        parent::__construct('Not found');
    }
}
