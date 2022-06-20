<?php

namespace Barberry;

use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;

class PostedFileTest extends \PHPUnit_Framework_TestCase
{
    public function testProvidesAccessToFileProperties()
    {
        $postedFile = new PostedFile(
            new UploadedFile(Utils::streamFor('GIF image'), 10, UPLOAD_ERR_OK, 'image.gif'),
            '/tmp/asD6yhq'
        );

        $this->assertEquals('GIF image', $postedFile->uploadedFile->getStream()->getContents());
        $this->assertEquals('/tmp/asD6yhq', $postedFile->tmpName);
        $this->assertEquals('image.gif', $postedFile->uploadedFile->getClientFilename());
    }
}
