<?php

class Plugin_Null implements Plugin_Interface_Converter {

    public function convert($bin) {
        return $bin;
    }
}
