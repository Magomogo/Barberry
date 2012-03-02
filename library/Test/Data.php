<?php

class Test_Data {

    public static function gif1x1() {
        return file_get_contents(dirname(dirname(__DIR__)) . '/test/data/1x1.gif');
    }
}
