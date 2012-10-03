<?php
namespace Barberry;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    public function testOptionsCanBeOverriden() {
        $config = new Config(__DIR__, array('httpHost' => 'bin.myhost.ch'));
        $this->assertEquals('bin.myhost.ch', $config->httpHost);
    }
}
