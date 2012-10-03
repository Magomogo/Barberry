<?php
namespace Barberry\Test;

class Stub {
    public static function create($className, $methodName = null, $returnValue = null) {
        $ret = self::newMock($className);

        if (!is_null($methodName)) {
            $ret
                ->expects(new \PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
                ->method($methodName)
                ->will(new \PHPUnit_Framework_MockObject_Stub_Return($returnValue));
        }

        return $ret;
    }

    public static function createMulti($className, array $methodNamesToValuesMap) {
        $ret = self::newMock($className);

        foreach ($methodNamesToValuesMap as $methodName => $value) {
            $ret
                ->expects(new \PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
                ->method($methodName)
                ->will(new \PHPUnit_Framework_MockObject_Stub_Return($value));
        }

        return $ret;
    }

//--------------------------------------------------------------------------------------------------

    private static function newMock($className) {
        $factory = new Util_Test_Stub_Factory;
        return $factory->publicGetMock($className);
    }
}

//==================================================================================================

class Util_Test_Stub_Factory extends \PHPUnit_Framework_TestCase {
    public function publicGetMock($className) {
        return $this->getMockBuilder($className)->disableOriginalConstructor()->getMock();
    }
}