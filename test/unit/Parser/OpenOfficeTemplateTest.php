<?php
namespace Barberry\Parser;
use Barberry\Test;

include_once dirname(dirname(dirname(__DIR__))) . '/externals/Tbs/tbs_class.php';

class OpenOfficeTemplateTest extends \PHPUnit_Framework_TestCase {

    public function testImplementsParserInterface() {
        $this->assertInstanceOf('Barberry\\Parser\\ParserInterface', self::p());
    }

    public function testSurvivesEmptyTemplate() {
        self::p()->parse('', array());
    }

    public function testLoadsBlockVariablesIntoTinyButStrongParser() {
        $tbs = $this->getMockBuilder('clsTinyButStrong')->disableOriginalConstructor()->getMock();
        $tbs->expects($this->once())->method('MergeBlock')->with(
            'arrayKey',
            array(
                'anyarray'
            )
        );

        self::p($tbs)->parse(
            '[]',
            array(
                'arrayKey' => array('anyarray')
            )
        );
    }

    public function testLoadsFieldVariablesIntoTinyButStrongParser() {
        $tbs = $this->getMockBuilder('clsTinyButStrong')->disableOriginalConstructor()->getMock();
        $tbs->expects($this->once())->method('MergeField')->with(
            'fieldKey', 'fieldValue'
        );

        self::p($tbs)->parse('[]',array('fieldKey' => 'fieldValue'));

    }

//--------------------------------------------------------------------------------------------------

    private static function p($tbs = null) {
        return new OpenOfficeTemplate(
            $tbs ?: Test\Stub::create('clsTinyButStrong'), ''
        );
    }
}
