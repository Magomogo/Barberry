<?php

class Response {

    /**
     * @var ContentType
     */
    public $contentType;
    public $body;

    public function __construct(ContentType $contentType, $body) {
        $this->contentType = $contentType;
        $this->body = $body;
    }

    public function send() {

    }
}