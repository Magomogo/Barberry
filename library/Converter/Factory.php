<?php

class Converter_Factory {

    public function nullConverter() {
        return new Converter_Null();
    }
}
