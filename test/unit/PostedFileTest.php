<?php
namespace Barberry;

class PostedFileTest extends \PHPUnit_Framework_TestCase {

    public function testProvidesAccessToBinAndFilenameProperties() {
        $postedFile = new PostedFile(Test\Data::gif1x1(), 'some filename');

        $this->assertEquals(Test\Data::gif1x1(), $postedFile->bin);
        $this->assertEquals('some filename', $postedFile->filename);
    }

    public function testReturnStandardExtension() {
        $postedFile = new PostedFile(Test\Data::gif1x1(), 'some filename');
        $this->assertEquals('gif', $postedFile->getStandardExtension());
    }

    public function testCalculatesMd5HashOfContent()
    {
        $postedFile = new PostedFile(Test\Data::gif1x1(), '');

        $this->assertSame('325472601571f31e1bf00674c368d335', $postedFile->md5);
    }
}
