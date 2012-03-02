<?php

class DispatcherTest extends PHPUnit_Framework_TestCase {

    public function testInstantiatesAController() {
        $this->assertInstanceOf(
            'Controller_Interface',
            $this->d()->dispatchRequest('/12345zx.jpg')
        );
    }

    public function testIdentifiesIdAtTheRequestUri() {
        $controller = $this->getMock('Controller_Interface');
        $controller->expects($this->once())->method('requestProcessed')->with('12345zx');

        $this->d($controller)->dispatchRequest('/12345zx.jpg');
    }

    public function testUnderstandsOutputContentTypeByExtensionGiven() {
        $controller = $this->getMock('Controller_Interface');
        $controller
                ->expects($this->once())
                ->method('requestProcessed')
                ->with(
                    $this->anything(), ContentType::jpeg()
                );

        $this->d($controller)->dispatchRequest('/12345zx.jpg');
    }

    public function testDelegatesFileLoading() {
        $loader = $this->getMock('FileLoader');
        $loader->expects($this->once())->method('process')->with(array('_FILES'));

        $this->d(null, $loader)->dispatchRequest('foo', array('_FILES'));
    }

//--------------------------------------------------------------------------------------------------

    private function d(Controller_Interface $controller = null, $loader = null) {
        return new Dispatcher(
            $controller ?: $this->getMock('Controller_Interface'),
            $loader ?: $this->getMock('FileLoader')
        );
    }
}
