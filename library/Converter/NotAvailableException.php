<?php

class Converter_NotAvailableException extends Exception {
    public function __construct($direction) {
        parent::__construct("Converter is not exist : " . $direction);
    }
}