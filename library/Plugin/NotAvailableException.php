<?php

class Plugin_NotAvailableException extends Exception {
    public function __construct($direction) {
        parent::__construct("Convertation is not possible : " . $direction);
    }
}