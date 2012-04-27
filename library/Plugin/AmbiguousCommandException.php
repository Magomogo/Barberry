<?php

class Plugin_AmbiguousCommandException extends Exception {
    public function __construct($commandString) {
        parent::__construct("Malformed command string: " . $commandString);
    }
}