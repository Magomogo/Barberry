<?php
namespace Barberry\Filter;

class Factory {

    public function odtFilter() {
        return $this->openOfficeTemplateParser();
    }

    public function otsFilter() {
        return $this->openOfficeTemplateParser();
    }

    public function ottFilter() {
        return $this->openOfficeTemplateParser();
    }

//--------------------------------------------------------------------------------------------------

    private function openOfficeTemplateParser() {
        return new OpenOfficeTemplate(new \clsTinyButStrong, '/tmp/');
    }
}
