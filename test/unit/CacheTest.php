<?php
namespace Barberry;

class CacheTest extends \PHPUnit_Framework_TestCase {

    /**
     * @param string $uri
     * @param string $expectedPath
     * @dataProvider uriCacheDataProvider
     */
    public function testConvertsUriToFilePath($uri, $expectedPath) {
        $cache = $this->getMockBuilder('Barberry\Cache')
            ->setMethods(['writeToFilesystem'])
            ->enableOriginalConstructor()
            ->setConstructorArgs(array('/'))
            ->getMock();

        $cache->expects($this->any())->method('writeToFilesystem')->with(
            '123', $expectedPath
        );

        $this->setExpectedException('Barberry\Cache\Exception');
        $cache->save('123', new Request($uri));
    }

    public static function uriCacheDataProvider() {
        return array(
            array('/asd23.gif', '/as/d2/3/asd23/asd23.gif'),
            array('/asd23.jpg', '/as/d2/3/asd23/asd23.jpg'),
            array('/asd23_1x1.gif', '/as/d2/3/asd23/asd23_1x1.gif'),
            array('/adm/asd23.gif', '/as/d2/3/adm/asd23/asd23.gif'),
            array('/adm/asd23_1x1.gif', '/as/d2/3/adm/asd23/asd23_1x1.gif'),
        );
    }
}
