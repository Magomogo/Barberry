<?php
namespace Barberry;

class RequestSourceTest extends \PHPUnit_Framework_TestCase {

    public function testHaveServerProperty() {
        $requestSource = new RequestSource();
        $this->assertEquals(true, property_exists($requestSource, '_SERVER'));
    }

    public function testServerPropertyHasArrayType() {
        $requestSource = new RequestSource();
        $this->assertInternalType('array', $requestSource->_SERVER);
    }

    public function testHavePostProperty() {
        $requestSource = new RequestSource();
        $this->assertEquals(true, property_exists($requestSource, '_POST'));
    }

    public function testPostPropertyHasArrayType() {
        $requestSource = new RequestSource();
        $this->assertInternalType('array', $requestSource->_POST);
    }

    public function testHaveFilesProperty() {
        $requestSource = new RequestSource();
        $this->assertEquals(true, property_exists($requestSource, '_FILES'));
    }

    public function testFilesPropertyHasArrayType() {
        $requestSource = new RequestSource();
        $this->assertInternalType('array', $requestSource->_FILES);
    }

    public function testPropertiesOverriddenOnlyIfHaveSameAsDefaultType() {
        $requestSource = new RequestSource(array(
            '_SERVER' => null,
        ));
        $this->assertInternalType(gettype($_SERVER), $requestSource->_SERVER);
    }

    public function testHaveProperServerKeys() {
        $requestSource = new RequestSource();
        $haveAllKeys = array_reduce(
            $this->serverKeys(),
            function($result, $propertyKey) use ($requestSource) {
                $result = $result && array_key_exists($propertyKey, $requestSource->_SERVER);
                return $result;
            },
            true
        );

        $this->assertEquals(true, $haveAllKeys);
    }

    public function testPropertiesCanBeOverridden() {
        $requestSource = new RequestSource(array(
            '_SERVER' => array(
                'REQUEST_URI' => '/some/url'
            ),
        ));
        $this->assertEquals('/some/url', $requestSource->_SERVER['REQUEST_URI']);
    }

    protected function serverKeys() {
        return array(
            'REQUEST_METHOD',
            'REQUEST_URI',
        );
    }

}
