<?php

class EnvironmentTest extends PHPUnit_Framework_TestCase {

    public function testAutoloaderWorks() {
        $this->assertTrue(class_exists('Dispatcher'));
    }

    public function testAutoloaderLoadsDirections() {
        $this->assertTrue(class_exists('TestClassDirection'));
    }

    /**
     * @dataProvider requiredWritableDirectories
     */
    public function testIsADirectoryExistAndWritable($dir) {
        $this->assertTrue(is_dir($dir));

        $ok = @file_put_contents($dir . 'check.txt', 'ok') !== false;
        @unlink($dir . 'check.txt');

        $this->assertTrue($ok);
    }

    public static function requiredWritableDirectories() {
        return array(
            array(Config::get()->directoryTemp),
            array(Config::get()->directoryCache),
            array(Config::get()->directoryStorage),
        );
    }
}
