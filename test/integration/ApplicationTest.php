<?php
namespace Barberry;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    public function testCanRun()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $a = new Application(new Config(__DIR__));
        $this->assertInstanceOf('Barberry\\Response', $a->run());
    }

    public function testCanGetResources()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $a = new Application(new Config(__DIR__));
        $this->assertInstanceOf('Barberry\\Resources', $a->resources());
    }

    public function testNullPostCaused400BadRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $a = new Application(new Config(__DIR__));

        $response = $a->run();

        $this->assertSame(400, $response->code);
    }
}
