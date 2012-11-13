<?php
namespace Barberry;

class PostedDataProcessorTest extends \PHPUnit_Framework_TestCase {

    public function testReadsFirstCorrectlyUploadedFileFromTemporaryDirectory() {
        $partialMock = $this->partiallyMockedProcessor();
        $partialMock->expects($this->once())
                ->method('readTempFile')
                ->with('/tmp/1254432ks3');

        $partialMock->process(array('myfile' => self::goodFileInPhpFilesArray()));
    }

    public function testSkipsIncorrectlyUploadedFile() {
        $partialMock = $this->partiallyMockedProcessor();
        $partialMock->expects($this->once())
                ->method('readTempFile')
                ->with('/tmp/1254432ks3');

        $partialMock->process(
            array(
                'badfile' => array_merge(
                    self::badFileInPhpFilesArray()
                ),
                'goodfile' => array_merge(
                    self::goodFileInPhpFilesArray()
                )
            )
        );
    }

    public function testCallsFilterInterfaceSpecifiedInConstructor() {
        $phpFiles = array('file' => self::goodFileInPhpFilesArray());
        $postVars = array('var' => 'test val');

        $filter = $this->getMock('Barberry\\Filter\\FilterInterface');
        $filter->expects($this->once())
            ->method('filter')
            ->with($postVars, array('file' => new PostedFile(Test\Data::gif1x1(), $phpFiles['file']['name'])))
            ->will($this->returnValue(new PostedFile('test content', 'test.txt')));

        $partialMock = self::partiallyMockedProcessor($filter);
        $this->assertEquals('test content', $partialMock->process($phpFiles, $postVars)->bin);
    }

    public function testReturnsPostedFileAndItsFilename() {
        $this->assertEquals(
            new PostedFile(Test\Data::gif1x1(), 'Name of a file.txt'),
            $this->partiallyMockedProcessor()->process(
                array(
                    'file' => self::goodFileInPhpFilesArray()
                )
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    private function partiallyMockedEmptyProcessor(\Barberry\Filter\FilterInterface $filter = null) {
        return $this->getMock(
            'Barberry\\PostedDataProcessor',
            array('readTempFile'),
            array($filter)
        );
    }

    private function partiallyMockedProcessor(\Barberry\Filter\FilterInterface $filter = null, $readFile = null) {
        $partialMock = $this->partiallyMockedEmptyProcessor($filter, $readFile);
        $partialMock->expects($this->any())->method('readTempFile')
                ->will($this->returnValue($readFile ?: Test\Data::gif1x1()));
        return $partialMock;
    }

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
            'tmp_name' => '/tmp/1214432ks3',
            'error' => UPLOAD_ERR_OK,
            'name' => 'some.jpg'
        );
    }

    private static function badFileInPhpFilesArray() {
        return array(
            'size' => 0,
            'error' => UPLOAD_ERR_CANT_WRITE
        );
    }
}
