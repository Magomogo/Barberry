<?php
namespace Barberry\Parser;

class Factory {

    public function otsParser() {
        return $this->openOfficeTemplateParser();
    }

    public function ottParser() {
        return $this->openOfficeTemplateParser();
    }

//--------------------------------------------------------------------------------------------------

    private function openOfficeTemplateParser() {
        return new OpenOfficeTemplate(
            new \clsTinyButStrong,
            \Barberry\Config::get()->directoryTemp
        );
    }
}
