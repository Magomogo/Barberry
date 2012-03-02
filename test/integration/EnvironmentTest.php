<?php

class EnvironmentTest extends PHPUnit_Framework_TestCase {

    public function testAutoloaderWorks() {
        $this->assertTrue(class_exists('Dispatcher'));
    }
}
