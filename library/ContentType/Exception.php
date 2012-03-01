<?php

class ContentType_Exception extends Exception {

    public function __construct($contentType) {
        parent::__construct('Unknown content type ' . $contentType);
    }
}
