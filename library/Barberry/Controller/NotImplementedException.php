<?php
namespace Barberry\Controller;

class NotImplementedException extends \Exception {

    public function __construct($msg) {
        parent::__construct($msg);
    }
}
