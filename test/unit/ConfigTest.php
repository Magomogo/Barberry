<?php

namespace Barberry;

use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testOptionsCanBeOverriden(): void
    {
        $adapter = new LocalFilesystemAdapter('/tmp/another/storage');
        $config = new Config($adapter);
        self::assertEquals($adapter, $config->storageAdapter);
    }

    public function testWorksWithNullAppPath(): void
    {
        $config = new Config();
        $this->expectNotToPerformAssertions();
    }
}
