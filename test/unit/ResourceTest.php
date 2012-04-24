<?php

class ResourcesTest extends PHPUnit_Framework_TestCase {

    public function testItIsSingleton() {
        $this->assertSame(Resources::get(), Resources::get());
    }

    public function testCache() {
        $this->assertInstanceOf('Cache', self::r()->cache());
    }

    public function testStorage() {
        $this->assertInstanceOf('Storage_Interface', self::r()->storage());
    }

    public function testRequest() {
        $this->assertInstanceOf('Request', self::r()->request($_SERVER, $_FILES, $_POST));
    }

    public function testProvidesSameInstanceOfAResource() {
        $r = self::r();
        $this->assertSame($r->cache(), $r->cache());
    }

//--------------------------------------------------------------------------------------------------

    private static function r() {
        return new Resources(Config::get());
    }
}
