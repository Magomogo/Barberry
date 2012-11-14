<?php
namespace Barberry\PostedFile;
use Barberry\Test;

class CollectionTest extends \PHPUnit_Framework_TestCase {

    public function testImplementsIteratorAndArrayAccessInterfaces() {
        $collection = new Collection(array());
        $this->assertInstanceOf('\\Iterator', $collection);
        $this->assertInstanceOf('\\ArrayAccess', $collection);
    }

    public function testCanBeIteratedThrowFiles() {
        $collection = $this->partiallyMockedCollection(
            array(
                'file' => self::goodFileInPhpFilesArray(),
                'image' => self::additionalFileInPhpFilesArray()
            )
        );

        $result = array();
        foreach ($collection as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals(2, count($result));
        $this->assertEquals('Name of a file.txt', $result['file']->filename);
        $this->assertEquals('another.gif', $result['image']->filename);
    }

    public function testReturnsPostedFileCreatedFromUploadedFilesPhpSpec() {
        $collection = $this->partiallyMockedCollection();
        $this->assertInstanceOf('Barberry\\PostedFile', $collection['file']);
    }

    public function testThrowsExceptionOnSetWrongPostedFile() {
        $this->setExpectedException('Barberry\\PostedFile\\CollectionException', 'Wrong type, should be PostedFile');
        $collection = $this->partiallyMockedCollection();
        $collection['file'] = new \stdClass();
    }

    public function testCanSetPostedFile() {
        $collection = $this->partiallyMockedCollection();
        $collection['file'] = new \Barberry\PostedFile('ssdgsdfg', 'test.txt');

        $this->assertEquals('test.txt', $collection['file']->filename);
    }

    public function testCanAddNewPostedFile() {
        $collection = $this->partiallyMockedCollection();
        $collection['image'] = new \Barberry\PostedFile('ssdgsdfg', 'test.txt');

        $collection->rewind();
        $collection->next();

        $this->assertEquals('test.txt', $collection->current()->filename);
    }

    public function testCanUnshiftPostedFileToTheBeginning() {
        $collection = $this->partiallyMockedCollection();
        $collection->unshift('image', new \Barberry\PostedFile('ssdgsdfg', 'test.txt'));

        $collection->rewind();
        $this->assertEquals('test.txt', $collection->current()->filename);

        $collection->next();
        $this->assertEquals('Name of a file.txt', $collection->current()->filename);
    }

    public function testCallsReadTempFileWhenIterating() {
        $collection = $this->partiallyMockedEmptyCollection();
        $collection->expects($this->once())
            ->method('readTempFile')
            ->with('/tmp/1254432ks3')
            ->will($this->returnValue(Test\Data::gif1x1()));

        foreach ($collection as $v) {
            $this->assertEquals(Test\Data::gif1x1(), $v->bin);
        }
    }

    public function testCallsReadTempFileOnlyWhenValueIsRetrieved() {
        $collection = $this->partiallyMockedEmptyCollection(
            array('file' => self::goodFileInPhpFilesArray(), 'image' => self::additionalFileInPhpFilesArray())
        );
        $collection->expects($this->once())
            ->method('readTempFile')
            ->with('/tmp/1254432ks1')
            ->will($this->returnValue(Test\Data::gif1x1()));

        $collection->rewind();
        $collection->next();

        $this->assertEquals(Test\Data::gif1x1(),  $collection->current()->bin);
    }

    public function testIgnoresBadFiles() {
        $collection = $this->partiallyMockedCollection(array('file' => self::badFileInPhpFilesArray()));

        foreach ($collection as $v) {
            $this->fail('Should not contain any files');
        }
    }

    public function testCanBeCreatedWithPostedFilesInConstructor() {
        $collection = new Collection(array('file' => new \Barberry\PostedFile(Test\Data::gif1x1(), 'test.gif')));
        $this->assertEquals(Test\Data::gif1x1(), $collection['file']->bin);
    }

//--------------------------------------------------------------------------------------------------

    private static function goodFileInPhpFilesArray() {
        return array(
            'size' => 1234,
            'tmp_name' => '/tmp/1254432ks3',
            'error' => UPLOAD_ERR_OK,
            'name' => 'Name of a file.txt'
        );
    }

    private static function additionalFileInPhpFilesArray() {
        return array(
            'size' => 123,
            'tmp_name' => '/tmp/1254432ks1',
            'error' => UPLOAD_ERR_OK,
            'name' => 'another.gif'
        );
    }

    private static function badFileInPhpFilesArray() {
        return array(
            'size' => 0,
            'error' => UPLOAD_ERR_CANT_WRITE
        );
    }

    private function partiallyMockedEmptyCollection(array $specs = null) {
        if ($specs === null) {
            $specs = array('file' => self::goodFileInPhpFilesArray());
        }

        return $this->getMock('Barberry\\PostedFile\\Collection', array('readTempFile'), array($specs));
    }

    private function partiallyMockedCollection(array $specs = null, $readFile = null) {
        $partialMock = $this->partiallyMockedEmptyCollection($specs);
        $partialMock->expects($this->any())->method('readTempFile')
            ->will($this->returnValue($readFile ?: Test\Data::gif1x1()));

        return $partialMock;
    }

}
