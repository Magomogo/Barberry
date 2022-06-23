<?php

namespace Barberry;

use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;

class PostedFileTest extends TestCase
{
    public function testProvidesAccessToFileProperties(): void
    {
        $postedFile = new PostedFile(
            new UploadedFile(Utils::streamFor('GIF image'), 10, UPLOAD_ERR_OK, 'image.gif'),
            '/tmp/asD6yhq'
        );

        self::assertEquals('GIF image', $postedFile->uploadedFile->getStream()->getContents());
        self::assertEquals('/tmp/asD6yhq', $postedFile->tmpName);
        self::assertEquals('image.gif', $postedFile->uploadedFile->getClientFilename());
    }
}
