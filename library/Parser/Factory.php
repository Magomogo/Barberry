<?php

class Parser_Factory {

    public function otsParser() {
        return $this->openOfficeTemplateParser();
    }

    public function ottParser() {
        return $this->openOfficeTemplateParser();
    }

//--------------------------------------------------------------------------------------------------

    private function openOfficeTemplateParser() {
        return new Parser_OpenOfficeTemplate(
            new clsTinyButStrong,
            Config::get()->directoryTemp
        );
    }
}
