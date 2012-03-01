<?php

class ConvertDirection {

    private $factoryMethod;

    public function __construct($sourceBinary, ContentType $destinationContentType) {
        $finfo = new finfo(FILEINFO_MIME);
        $this->factoryMethod =
            ContentType::createByContentTypeString($finfo->buffer($sourceBinary))->standartExtention().
            "To".
            ucfirst($destinationContentType->standartExtention());

    }

    public function initConverter($factory) {
        if(method_exists($factory, $this->factoryMethod)) {
            return $factory->{$this->factoryMethod}();
        }
        throw new Converter_NotAvailableException($this->factoryMethod);
    }
}
