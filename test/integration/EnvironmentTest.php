<?php

class EnvironmentTest extends PHPUnit_Framework_TestCase {

    public function testAutoloaderWorks() {
        new Dispatcher($this->getMock('Storage_Interface'));
    }
}
