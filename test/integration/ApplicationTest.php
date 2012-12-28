<?php
namespace Barberry;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    public function testCanRun()
    {
        $requestSource = new RequestSource(array(
            '_SERVER' => array('REQUEST_METHOD' => 'GET')
        ));
        $a = new Application(new Config(__DIR__), null, $requestSource);
        $this->assertInstanceOf('Barberry\\Response', $a->run());
    }
}
