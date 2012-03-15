<?php

class Converter_Factory {

    /**
     * @return Converter_Null
     */
    public function nullConverter() {
        return new Converter_Null();
    }

    /**
     * @return Converter_OpenOffice
     */
    public function otsToXls() {
        return new Converter_OpenOffice(ContentType::xls(), Config::get()->directoryTemp);
    }

    /**
     * @return Converter_OpenOffice
     */
    public function odsToXls() {
        return new Converter_OpenOffice(ContentType::xls(), Config::get()->directoryTemp);
    }

    /**
     * @return Converter_OpenOffice
     */
    public function ottToDoc() {
        return new Converter_OpenOffice(ContentType::doc(), Config::get()->directoryTemp);
    }

    /**
     * @return Converter_OpenOffice
     */
    public function odtToDoc() {
        return new Converter_OpenOffice(ContentType::doc(), Config::get()->directoryTemp);
    }
}
