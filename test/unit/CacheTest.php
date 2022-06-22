<?php

namespace Barberry;

use GuzzleHttp\Psr7\Utils;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private static $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        self::$filesystem = vfsStream::setup('root', null, [
            'cache' => []
        ]);
    }

    /**
     * @param string $uri
     * @param string $expectedPath
     * @dataProvider uriCacheDataProvider
     */
    public function testConvertsUriToFilePath(string $uri, string $expectedPath): void
    {
        $cache = $this->getMockBuilder(Cache::class)
            ->setMethods(['writeToFilesystem'])
            ->enableOriginalConstructor()
            ->setConstructorArgs([self::$filesystem->url() . '/cache'])
            ->getMock();

        $cache
            ->expects($this->any())
            ->method('writeToFilesystem')
            ->with('123', $expectedPath);

        $cache->save('123', new Request($uri));
    }

    public function testStreamDataCanBeSaved(): void
    {
        $cache = new Cache(self::$filesystem->url() . '/cache');
        $cache->save(Utils::streamFor('Cached content'), new Request('/a1b2c3d4.gif'));

        self::assertEquals('Cached content', file_get_contents(
            self::$filesystem->url() . '/cache/a1/b2/c3/a1b2c3d4/a1b2c3d4.gif'
        ));
    }

    public static function uriCacheDataProvider(): array
    {
        return [
            ['/asd23.gif', '/as/d2/3/asd23/asd23.gif'],
            ['/asd23.jpg', '/as/d2/3/asd23/asd23.jpg'],
            ['/asd23_1x1.gif', '/as/d2/3/asd23/asd23_1x1.gif'],
            ['/adm/asd23.gif', '/as/d2/3/adm/asd23/asd23.gif'],
            ['/adm/asd23_1x1.gif', '/as/d2/3/adm/asd23/asd23_1x1.gif']
        ];
    }
}
