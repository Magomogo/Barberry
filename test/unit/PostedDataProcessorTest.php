<?php
namespace Barberry;

class PostedDataProcessorTest extends \PHPUnit_Framework_TestCase {

    public function testCallsFilterInterfaceSpecifiedInConstructor() {
        $postVars = array('var' => 'test val');

        $filter = $this->createMock('Barberry\\Filter\\FilterInterface');
        $filter->expects($this->once())
            ->method('filter')
            ->with($this->isInstanceOf('Barberry\\PostedFile\\Collection'), $postVars)
            ->will(
                $this->returnCallback(
                    function (\Barberry\PostedFile\Collection $files, $vars) {
                        $files['file'] = new PostedFile('test content', 'test.txt');
                    }
                )
            );

        $processor = new PostedDataProcessor($filter);
        $this->assertEquals('test content', $processor->process(array(), $postVars)->bin);
    }

    public function testReturnsFirstPostedFileFromCollection() {
        $mock = $this->getMockBuilder('Barberry\\PostedDataProcessor')
            ->setMethods(['createCollection'])
            ->enableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('createCollection')
            ->with(array('files'))
            ->will(
                $this->returnValue(
                    new PostedFile\Collection(
                        array(
                            'file' => new PostedFile('some', 'Name of a file.txt'),
                            'image' => new PostedFile(Test\Data::gif1x1(), 'test.gif')
                        )
                    )
                )
            );

        $this->assertEquals('Name of a file.txt', $mock->process(array('files'))->filename);
    }

}
