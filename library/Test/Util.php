<?php

class Test_Util {

    public static function assertOpenOfficeServiceIsAvailable() {
        $fp = @fsockopen('127.0.0.1', '2002');
        if (!$fp) {
            PHPUnit_Framework_TestCase::markTestSkipped('Open office service isn\'t started');
        }
        fclose($fp);
    }
}
