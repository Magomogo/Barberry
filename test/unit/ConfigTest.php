<?php

namespace Barberry;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testOptionsCanBeOverriden(): void
    {
        $config = new Config(__DIR__, '/test_config.php');
        self::assertEquals('/usr/another/storage', $config->directoryStorage);
    }
}
