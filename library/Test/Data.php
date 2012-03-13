<?php

class Test_Data {

    public static function gif1x1() {
        return file_get_contents(dirname(dirname(__DIR__)) . '/test/data/1x1.gif');
    }

    public static function ottTemplate() {
        return file_get_contents(dirname(dirname(__DIR__)) . '/test/data/document1.ott');
    }

    public static function otsTemplate() {
        return file_get_contents(dirname(dirname(__DIR__)) . '/test/data/spreadsheet1.ots');
    }

    public static function xlsSpreadsheet() {
        return file_get_contents(dirname(dirname(__DIR__)) . '/test/data/spreadsheet1.xls');
    }
}
