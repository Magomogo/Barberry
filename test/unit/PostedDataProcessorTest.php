<?php

class PostedDataProcessorTest extends PHPUnit_Framework_TestCase {

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

    public function testUtilizesFactoryToCreateAParserForPostedTemplate() {
        $parserFactory = $this->getMock('Parser_Factory');
        $parserFactory->expects($this->once())
                ->method('ottParser')
                ->will($this->returnValue(Test_Stub::create('Parser_Interface')));

        $processor = $this->partiallyMockedProcessor($parserFactory, Test_Data::ottTemplate());
        $processor->process(
            array(
                'file' => self::goodFileInPhpFilesArray()
            ),
            array('vars')
        );
    }

    public function testUtilizesParserToParsePostedTemplate() {
        $parser = $this->getMock('Parser_Interface');
        $parser->expects($this->once())
                ->method('parse')
                ->with(Test_Data::ottTemplate(), array('vars'))
                ->will($this->returnValue('Parse result'));

        $processor = $this->partiallyMockedProcessor(
            Test_Stub::create('Parser_Factory', 'ottParser', $parser),
            Test_Data::ottTemplate()
        );
        $processor->expects($this->any())->method('readTempFile')->will($this->returnValue(
            Test_Data::ottTemplate()
        ));

        $this->assertEquals(
            array('content' => 'Parse result', 'filename' => 'Name of a file.txt'),
            $processor->process(
                array(
                    'file' => self::goodFileInPhpFilesArray()
                ),
                array('vars')
            )
        );
    }

    public function testReturnsPostedFileAndItsFilename() {
        $this->assertEquals(
            array(
                'content' => Test_Data::gif1x1(),
                'filename' => 'Name of a file.txt',
            ),
            $this->partiallyMockedProcessor()->process(
                array(
                    'file' => self::goodFileInPhpFilesArray()
                )
            )
        );

    }

//--------------------------------------------------------------------------------------------------

    private function partiallyMockedProcessor($parserFactory = null, $readFile = null) {
        $partialMock = $this->getMock(
            'PostedDataProcessor',
            array('readTempFile'),
            array($parserFactory ?: new Parser_Factory)
        );
        $partialMock->expects($this->any())->method('readTempFile')
                ->will($this->returnValue($readFile ?: Test_Data::gif1x1()));
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

    private static function badFileInPhpFilesArray() {
        return array(
            'size' => 0,
            'error' => UPLOAD_ERR_CANT_WRITE
        );
    }
}
