<?php

namespace Barberry;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testOptionsCanBeOverriden(): void
    {
        $config = new Config(__DIR__, '/test_config.php');
        self::assertEquals('/tmp/another/storage', $config->directoryStorage);
    }

    public function testWorksWithNullAppPath(): void
    {
        $config = new Config();
        $this->expectNotToPerformAssertions();
    }
}
