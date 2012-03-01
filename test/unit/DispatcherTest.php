<?php

class DispatcherTest extends PHPUnit_Framework_TestCase {

    public function testInstantiatesAController() {
        $this->assertInstanceOf(
            'Controller',
            $this->d()->dispatch('/12345zx.jpg', array())
        );
    }

    public function testIdentifiesIdAtTheRequestUri() {
        $d = $this->d();
        $controller = $d->dispatch('/12345zx.jpg', array());
        $this->assertAttributeEquals('12345zx', 'entityId', $controller);
    }

    public function testUnderstandsOutputContentTypeByExtensionGiven() {
        $d = $this->d();
        $controller = $d->dispatch('/12345zx.jpg', array());
        $this->assertAttributeEquals(
            ContentType::createByExtention('jpeg'),
            'outputContentType',
            $controller
        );
    }

//--------------------------------------------------------------------------------------------------

    private function d(Storage_Interface $storageMock = null) {
        return new Dispatcher(
            $storageMock ?: $this->getMock('Storage_Interface')
        );
    }
}
