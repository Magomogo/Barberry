<?php
namespace Barberry;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    public function testCanRun()
    {
        $a = new Application(new Config(__DIR__));
        $this->assertInstanceOf('Barberry\\Response', $a->run());
    }
}
