<?php

class FileLoaderTest extends PHPUnit_Framework_TestCase {

    public function testReadsFirstCorrectlyUploadedFileFromTemporaryDirectory() {
        $partialMock = $this->partiallyMockedLoader();
        $partialMock->expects($this->once())->method('readTempFile')->with('/tmp/1254432ks3');

        $partialMock->process(array('myfile' => self::aFileInPhpFilesArray()));
    }

    public function testSkipsIncorrectlyUploadedFile() {
        $partialMock = $this->partiallyMockedLoader();
        $partialMock->expects($this->once())->method('readTempFile')->with('/tmp/goodFileHere');

        $partialMock->process(
            array(
                'badfile' => array_merge(
                    self::aFileInPhpFilesArray(), array('error' => UPLOAD_ERR_PARTIAL)
                ),
                'goodfile' => array_merge(
                    self::aFileInPhpFilesArray(), array('tmp_name' => '/tmp/goodFileHere')
                )
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    private function partiallyMockedLoader() {
        $partialMock = $this->getMock('FileLoader', array('readTempFile'));
        return $partialMock;
    }

    private static function aFileInPhpFilesArray() {
        return array(
            'name' => 'facepalm.gif',
            'type' => 'image/gif',
            'size' => 1234,
            'tmp_name' => '/tmp/1254432ks3',
            'error' => UPLOAD_ERR_OK
        );
    }
}
