<?php
namespace Barberry\Cache;

class Exception extends \Exception {

    public function __construct($path, $reason = '') {
        parent::__construct('Cache path [' . $path . '] was not created.' .
            (!empty($reason) ? ' Reason: ' . $reason : '')
        );
    }
}
