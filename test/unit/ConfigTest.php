<?php
namespace Barberry;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    public function testConfigIsSingleton() {
        $this->assertSame(
            Config::get(),
            Config::get()
        );
    }

    public function testOptionsCanBeOverriden() {
        $config = new Config(array('httpHost' => 'bin.myhost.ch'));
        $this->assertEquals('bin.myhost.ch', $config->httpHost);
    }
}
