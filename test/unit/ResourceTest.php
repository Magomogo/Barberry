<?php
namespace Barberry;

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

//--------------------------------------------------------------------------------------------------

    private static function r() {
        return new Resources(new Config(__DIR__));
    }
}
