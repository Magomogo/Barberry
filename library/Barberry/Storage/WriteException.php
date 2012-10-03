<?php
namespace Barberry\Storage;

class WriteException extends \Exception {

    public function __construct($id) {
        parent::__construct('Document [' . $id . '] cannot be written.');
    }
}
