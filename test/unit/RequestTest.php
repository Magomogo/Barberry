<?php

namespace Barberry;

use GuzzleHttp\Psr7\UploadedFile;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractsId()
    {
        $this->assertEquals('12345zx', self::request('/12345zx.jpg')->id);
        $this->assertEquals('12345zx', self::request('/12345zx')->id);
    }

    public function testExtractsGroup()
    {
        $this->assertEquals(
            'adm',
            self::request('/adm/12345zx.jpg')->group
        );
    }

    public function testUnderstandsOutputContentTypeByExtension()
    {
        $this->assertEquals(
            ContentType::jpeg(),
            self::request('/12345zx.jpg')->contentType
        );
        $this->assertEquals(
            ContentType::mp4(),
            self::request('/test.mp4')->contentType
        );
    }

    public function testExtractsCommandStringFromUri()
    {
        $r = self::request('/123erwe34_175x75_bgFFF_bw.jpg');
        $this->assertEquals('175x75_bgFFF_bw', $r->commandString);
    }

    public function testExtractsAll()
    {
        $r = self::request('/adm/123erwe34_175x75_bgFFF_bw.jpg');
        $this->assertEquals('adm', $r->group);
        $this->assertEquals('123erwe34', $r->id);
        $this->assertEquals('175x75_bgFFF_bw', $r->commandString);
        $this->assertEquals(ContentType::jpeg(), $r->contentType);
    }

    public function testProvidesAccessToPostedFile()
    {
        $postedFile = new PostedFile(
            new UploadedFile('123', 1024, UPLOAD_ERR_OK, 'Text.txt', 'text/plain'),
            '/tmp/xGhoPq'
        );
        $request = new Request('', $postedFile);

        $this->assertEquals($postedFile, $request->postedFile);
    }

    public function testKeepsOriginalUri()
    {
        $r = self::request('/adm/123erwe34_absrs.jpg');
        $this->assertEquals('123erwe34_absrs.jpg', $r->originalBasename);
    }

    public function testKeepsOriginalUriOfGroupRequest()
    {
        $r = self::request('/123erwe34_absrs.jpg');
        $this->assertEquals('123erwe34_absrs.jpg', $r->originalBasename);
    }

    public function testRecognizesCommandWhenExtensionMissed()
    {
        $r = self::request('/123erwe34_absrs');
        $this->assertEquals('absrs', $r->commandString);
    }

    public function testEmptyCommandStringAsString()
    {
        $r = self::request('/123erwe34.gif');
        $this->assertSame('', $r->commandString);
    }

    private static function request($uri)
    {
        return new Request($uri);
    }
}
