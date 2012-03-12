<?php

class Storage_WriteException extends Exception {

    public function __construct($id) {
        parent::__construct('Document [' . $id . '] cannot be written.');
    }
}
