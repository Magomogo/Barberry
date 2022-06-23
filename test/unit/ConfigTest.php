<?php

namespace Barberry;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testOptionsCanBeOverriden()
    {
        $config = new Config(__DIR__, '/test_config.php');
        $this->assertEquals('/usr/another/storage', $config->directoryStorage);
    }
}
