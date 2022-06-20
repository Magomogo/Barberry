<?php

namespace Barberry;

use Barberry\Filter\FilterInterface;
use Barberry\PostedFile\Collection;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use Mockery as m;

class PostedDataProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testCallsFilterInterfaceSpecifiedInConstructor()
    {
        $postVars = ['var' => 'test val'];

        $filterMock = m::mock(FilterInterface::class);
        $filterMock
            ->shouldReceive('filter')
            ->with(m::type(Collection::class), $postVars)
            ->andReturnUsing(
                function (\Barberry\PostedFile\Collection $files, $vars) {
                    $files['file'] = new PostedFile(
                        new UploadedFile(Utils::streamFor('test content'), 10, UPLOAD_ERR_OK, 'test.txt'),
                        '/tmp/asD6yhq'
                    );
                }
            )
            ->once();

        $processor = new PostedDataProcessor($filterMock);
        $this->assertEquals(
            'test content',
            $processor->process([], $postVars)->uploadedFile->getStream()->getContents()
        );
    }

    public function testReturnsFirstPostedFileFromCollection()
    {
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
                            'file' => new PostedFile(
                                new UploadedFile(Utils::streamFor('some'), 10, UPLOAD_ERR_OK, 'Name of a file.txt'),
                                '/tmp/asD6yhq'
                            ),
                            'image' => new PostedFile(
                                new UploadedFile(Utils::streamFor('GIF binary'), 10, UPLOAD_ERR_OK, 'test.gif'),
                                '/tmp/zAq8ugi'
                            )
                        )
                    )
                )
            );

        $this->assertEquals(
            'Name of a file.txt',
            $mock->process(['files'])->uploadedFile->getClientFilename()
        );
    }

}
