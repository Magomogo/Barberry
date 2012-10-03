<?php
namespace Barberry\Plugin;

class NotAvailableException extends \Exception {
    public function __construct($direction) {
        parent::__construct("Convertation is not possible : " . $direction);
    }
}