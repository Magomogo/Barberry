<?php
namespace Barberry\Controller;

class NullPostException extends \Exception {

    public function __construct() {
        parent::__construct('No data POSTed');
    }
}
