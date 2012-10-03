<?php
namespace Barberry\Controller;

class NotFoundException extends \Exception {

    public function __construct() {
        parent::__construct('Not found');
    }
}
