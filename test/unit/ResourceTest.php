<?php

namespace Barberry;

use Barberry\Filter;
use Barberry\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class ResourcesTest extends TestCase
{
    public function testCache(): void
    {
        self::assertInstanceOf(Cache::class, self::r()->cache());
    }

    public function testStorage(): void
    {
        self::assertInstanceOf(StorageInterface::class, self::r()->storage());
    }

    public function testRequest(): void
    {
        self::assertInstanceOf(Request::class, self::r()->request($_SERVER, $_FILES, $_POST));
    }

    public function testProvidesSameInstanceOfAResource(): void
    {
        $r = self::r();
        self::assertSame($r->cache(), $r->cache());
    }

    public function testPassesDataFilterToPostedDataProcessor(): void
    {
        $filter = $this->createMock('Barberry\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filter');

        self::r($filter)->request();
    }

    private static function r(Filter\FilterInterface $filter = null): Resources
    {
        return new Resources(new Config(__DIR__), $filter);
    }
}
