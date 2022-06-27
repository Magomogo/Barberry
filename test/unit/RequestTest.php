<?php

namespace Barberry;

use GuzzleHttp\Psr7\UploadedFile;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testExtractsId(): void
    {
        self::assertEquals('12345zx', self::request('/12345zx.jpg')->id);
        self::assertEquals('12345zx', self::request('/12345zx')->id);
    }

    public function testExtractsGroup(): void
    {
        self::assertEquals(
            'adm',
            self::request('/adm/12345zx.jpg')->group
        );
    }

    public function testUnderstandsOutputContentTypeByExtension(): void
    {
        self::assertEquals(
            ContentType::jpeg(),
            self::request('/12345zx.jpg')->contentType
        );
        self::assertEquals(
            ContentType::mp4(),
            self::request('/test.mp4')->contentType
        );
    }

    public function testExtractsCommandStringFromUri(): void
    {
        $r = self::request('/123erwe34_175x75_bgFFF_bw.jpg');
        self::assertEquals('175x75_bgFFF_bw', $r->commandString);
    }

    public function testExtractsAll(): void
    {
        $r = self::request('/adm/123erwe34_175x75_bgFFF_bw.jpg');
        self::assertEquals('adm', $r->group);
        self::assertEquals('123erwe34', $r->id);
        self::assertEquals('175x75_bgFFF_bw', $r->commandString);
        self::assertEquals(ContentType::jpeg(), $r->contentType);
    }

    public function testProvidesAccessToPostedFile(): void
    {
        $postedFile = new PostedFile(
            new UploadedFile('123', 1024, UPLOAD_ERR_OK, 'Text.txt', 'text/plain'),
            '/tmp/xGhoPq'
        );
        $request = new Request('', $postedFile);

        self::assertEquals($postedFile, $request->postedFile);
    }

    public function testKeepsOriginalUri(): void
    {
        $r = self::request('/adm/123erwe34_absrs.jpg');
        self::assertEquals('123erwe34_absrs.jpg', $r->originalBasename);
    }

    public function testKeepsOriginalUriOfGroupRequest(): void
    {
        $r = self::request('/123erwe34_absrs.jpg');
        self::assertEquals('123erwe34_absrs.jpg', $r->originalBasename);
    }

    public function testRecognizesCommandWhenExtensionMissed(): void
    {
        $r = self::request('/123erwe34_absrs');
        self::assertEquals('absrs', $r->commandString);
    }

    public function testEmptyCommandStringAsString(): void
    {
        $r = self::request('/123erwe34.gif');
        self::assertSame('', $r->commandString);
    }

    private static function request($uri): Request
    {
        return new Request($uri);
    }
}
