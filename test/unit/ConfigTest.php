<?php
namespace Barberry;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    public function testOptionsCanBeOverriden() {
        $config = new Config(__DIR__, '/test_config.php');
        $this->assertEquals('/usr/another/storage', $config->directoryStorage);
    }
}
