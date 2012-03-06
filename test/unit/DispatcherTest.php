<?php

class DispatcherTest extends PHPUnit_Framework_TestCase {

    public function testInstantiatesAController() {
        $this->assertInstanceOf(
            'Controller_Interface',
            self::d()->dispatchRequest('/12345zx.jpg')
        );
    }

    public function testIdentifiesIdAtTheRequestUri() {
        $controller = $this->getMock('Controller_Interface');
        $controller->expects($this->once())->method('requestDispatched')->with('12345zx');

        self::d($controller)->dispatchRequest('/12345zx.jpg');
    }

    public function testUnderstandsOutputContentTypeByExtensionGiven() {
        $controller = $this->getMock('Controller_Interface');
        $controller
                ->expects($this->once())
                ->method('requestDispatched')
                ->with(
                    $this->anything(), ContentType::jpeg()
                );

        self::d($controller)->dispatchRequest('/12345zx.jpg');
    }

    public function testDelegatesPostedFileProcessing() {
        $loader = $this->getMockBuilder('PostedDataProcessor')->disableOriginalConstructor()->getMock();
        $loader->expects($this->once())->method('process')->with(array('_FILES'), array('_REQUEST'));

        self::d(null, $loader)->dispatchRequest('foo', array('_FILES'), array('_REQUEST'));
    }

//--------------------------------------------------------------------------------------------------

    private static function d(Controller_Interface $controller = null, $loader = null) {
        return new Dispatcher(
            $controller ?: Test_Stub::create('Controller_Interface'),
            $loader ?: Test_Stub::create('PostedDataProcessor')
        );
    }
}
