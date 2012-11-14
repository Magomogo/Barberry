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

}
