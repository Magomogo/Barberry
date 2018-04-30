<?php
namespace Barberry;
use Barberry\Filter;

class ResourcesTest extends \PHPUnit_Framework_TestCase {

    public function testCache() {
        $this->assertInstanceOf('Barberry\\Cache', self::r()->cache());
    }

    public function testStorage() {
        $this->assertInstanceOf('Barberry\\Storage\\StorageInterface', self::r()->storage());
    }

    public function testRequest() {
        $this->assertInstanceOf('Barberry\\Request', self::r()->request($_SERVER, $_FILES, $_POST));
    }

    public function testProvidesSameInstanceOfAResource() {
        $r = self::r();
        $this->assertSame($r->cache(), $r->cache());
    }

    public function testPassesDataFilterToPostedDataProcessor() {
        $filter = $this->createMock('Barberry\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filter');

        self::r($filter)->request();
    }

//--------------------------------------------------------------------------------------------------

    private static function r(Filter\FilterInterface $filter = null) {
        return new Resources(new Config(__DIR__), $filter);
    }
}
