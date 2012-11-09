<?php
namespace Barberry;

class PostedFileTest extends \PHPUnit_Framework_TestCase {

    public function testProvidesToBinAndFilenameProperties() {
        $postedFile = new PostedFile('some binary data', 'some filename');

        $this->assertEquals('some binary data', $postedFile->bin);
        $this->assertEquals('some filename', $postedFile->filename);
    }

}
