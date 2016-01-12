<?php
namespace Barberry\Storage;

class WriteException extends \Exception {

    public function __construct($id, $reason = '') {
        parent::__construct('Document [' . $id . '] cannot be written' . (!empty($reason) ? ': ' . $reason : '') . '.');
    }
}
