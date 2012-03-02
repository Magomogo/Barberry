<?php

class Converter_Null implements Converter_Interface {

    public function convert($bin) {
        return $bin;
    }
}
