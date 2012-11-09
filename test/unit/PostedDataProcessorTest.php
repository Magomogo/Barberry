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

    public function testUtilizesFactoryToCreateAParserForPostedTemplate() {
        $filterFactory = $this->getMock('Barberry\\Filter\\Factory');
        $filterFactory->expects($this->once())
                ->method('ottFilter')
                ->will($this->returnValue(Test\Stub::create('Barberry\\Filter\\FilterInterface')));

        $processor = $this->partiallyMockedProcessor($filterFactory, Test\Data::ottTemplate());
        $processor->process(
            array(
                'file' => self::goodFileInPhpFilesArray()
            ),
            array('vars')
        );
    }

    public function testUtilizesParserToParsePostedTemplate() {
        $filter = $this->getMock('Barberry\\Filter\\FilterInterface');
        $filter->expects($this->once())
                ->method('filter')
                ->with(
                    array('vars'),
                    array(
                        'file' => new PostedFile(Test\Data::ottTemplate(), 'Name of a file.txt'),
                        'image' => new PostedFile(Test\Data::gif1x1(), 'some.jpg')
                    )
                )
                ->will($this->returnValue(new PostedFile('Parse result', 'Name of a file.txt')));

        $processor = $this->partiallyMockedEmptyProcessor(
            Test\Stub::create('Barberry\\Filter\\Factory', 'ottFilter', $filter)
        );
        $processor->expects($this->at(0))->method('readTempFile')->with('/tmp/1254432ks3')->will($this->returnValue(
            Test\Data::ottTemplate()
        ));
        $processor->expects($this->at(1))->method('readTempFile')->with('/tmp/1214432ks3')->will($this->returnValue(
            Test\Data::gif1x1()
        ));

        $this->assertEquals(
            new PostedFile('Parse result', 'Name of a file.txt'),
            $processor->process(
                array(
                    'file' => self::goodFileInPhpFilesArray(),
                    'image' => self::additionalFileInPhpFilesArray()
                ),
                array('vars')
            )
        );
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

    private function partiallyMockedEmptyProcessor($filterFactory = null) {
        return $this->getMock(
            'Barberry\\PostedDataProcessor',
            array('readTempFile'),
            array($filterFactory ?: new Filter\Factory)
        );
    }

    private function partiallyMockedProcessor($filterFactory = null, $readFile = null) {
        $partialMock = $this->partiallyMockedEmptyProcessor($filterFactory, $readFile);
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
