<?php

class PostedDataProcessorTest extends PHPUnit_Framework_TestCase {

    public function testReadsFirstCorrectlyUploadedFileFromTemporaryDirectory() {
        $partialMock = $this->partiallyMockedProcessor();
        $partialMock->expects($this->once())->method('readTempFile')->with('/tmp/1254432ks3');

        $partialMock->process(array('myfile' => self::gifFileInPhpFilesArray()));
    }

    public function testSkipsIncorrectlyUploadedFile() {
        $partialMock = $this->partiallyMockedProcessor();
        $partialMock->expects($this->once())->method('readTempFile')->with('/tmp/goodFileHere');

        $partialMock->process(
            array(
                'badfile' => array_merge(
                    self::gifFileInPhpFilesArray(), array('error' => UPLOAD_ERR_PARTIAL)
                ),
                'goodfile' => array_merge(
                    self::gifFileInPhpFilesArray(), array('tmp_name' => '/tmp/goodFileHere')
                )
            )
        );
    }

    public function testUtilizesFactoryToCreateAParserForPostedTemplate() {
        $parserFactory = $this->getMock('Parser_Factory');
        $parserFactory->expects($this->once())
                ->method('odtParser')
                ->will($this->returnValue(Test_Stub::create('Parser_Interface')));

        $processor = $this->partiallyMockedProcessor($parserFactory);
        $processor->process(
            array(
                'file' => self::odtFileInPhpFilesArray()
            ),
            array('vars')
        );
    }

    public function testUtilizesParserToParsePostedTemplate() {
        $parser = $this->getMock('Parser_Interface');
        $parser->expects($this->once())
                ->method('parse')
                ->with('10011', array('vars'))
                ->will($this->returnValue('Parse result'));

        $processor = $this->partiallyMockedProcessor(
            Test_Stub::create('Parser_Factory', 'odtParser', $parser)
        );

        $this->assertEquals(
            'Parse result',
            $processor->process(
                array(
                    'file' => self::odtFileInPhpFilesArray()
                ),
                array('vars')
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    private function partiallyMockedProcessor($parserFactory = null) {
        $partialMock = $this->getMock(
            'PostedDataProcessor',
            array('readTempFile'),
            array($parserFactory ?: new Parser_Factory)
        );
        $partialMock->expects($this->any())->method('readTempFile')
                ->will($this->returnValue('10011'));
        return $partialMock;
    }

    private static function gifFileInPhpFilesArray() {
        return array(
            'name' => 'facepalm.gif',
            'type' => 'image/gif',
            'size' => 1234,
            'tmp_name' => '/tmp/1254432ks3',
            'error' => UPLOAD_ERR_OK
        );
    }

    private static function odtFileInPhpFilesArray() {
        return array(
            'name' => 'template.odt',
            'type' => 'application/vnd.oasis.opendocument.spreadsheet-template',
            'size' => 443,
            'tmp_name' => '/tmp/wer2342',
            'error' => UPLOAD_ERR_OK
        );
    }
}
