<?php

class Storage_NotFoundException extends Exception {

    public function __construct($id) {
        parent::__construct('Document [' . $id . '] was not found.');
    }
}
