<?php

class ConvertDirection {

    private $factoryMethod;

    public function __construct($sourceBinary, ContentType $destinationContentType) {
        $sourceExt = ContentType::byString($sourceBinary)->standartExtention();
        $destinationExt = $destinationContentType->standartExtention();

        if ($sourceExt != $destinationExt) {
            $this->factoryMethod = $sourceExt . 'To' . ucfirst($destinationExt);
        } else {
            $this->factoryMethod = 'nullConverter';
        }

    }

    /**
     * @param $factory
     * @return Converter_Interface
     * @throws Converter_NotAvailableException
     */
    public function initConverter($factory) {

        if(method_exists($factory, $this->factoryMethod)) {
            return $factory->{$this->factoryMethod}();
        }
        throw new Converter_NotAvailableException($this->factoryMethod);
    }
}
