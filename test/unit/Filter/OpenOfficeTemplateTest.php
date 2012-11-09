<?php
namespace Barberry\Filter;
use Barberry;
use Barberry\Test;

include_once dirname(dirname(dirname(__DIR__))) . '/externals/Tbs/tbs_class.php';

class OpenOfficeTemplateTest extends \PHPUnit_Framework_TestCase {

    public function testImplementsParserInterface() {
        $this->assertInstanceOf('Barberry\\Filter\\FilterInterface', self::p());
    }

    public function testSurvivesEmptyTemplate() {
        self::p()->filter(array());
    }

    public function testLoadsBlockVariablesIntoTinyButStrongParser() {
        $tbs = $this->getMockBuilder('clsTinyButStrong')->disableOriginalConstructor()->getMock();
        $tbs->expects($this->once())->method('MergeBlock')->with(
            'arrayKey',
            array(
                'anyarray'
            )
        );

        self::p($tbs)->filter(
            array(
                'arrayKey' => array('anyarray')
            ),
            array(new \Barberry\PostedFile('[]'))
        );
    }

    public function testLoadsFieldVariablesIntoTinyButStrongParser() {
        $tbs = $this->getMockBuilder('clsTinyButStrong')->disableOriginalConstructor()->getMock();
        $tbs->expects($this->once())->method('MergeField')->with(
            'fieldKey', 'fieldValue'
        );

        self::p($tbs)->filter(array('fieldKey' => 'fieldValue'), array(new \Barberry\PostedFile('[]')));

    }

    public function testLoadsFilesIntoTinyButStrongParser() {
        $tbs = $this->getMockBuilder('clsTinyButStrong')->disableOriginalConstructor()->getMock();
        $tbs->expects($this->once())
            ->method('MergeField')
            ->with('image', $this->logicalAnd($this->stringStartsWith('/tmp/ooparser_'), $this->stringEndsWith('.gif')));

        $p = self::p($tbs);
        $p->filter(
            array(),
            array(
                'file' => new \Barberry\PostedFile('[]'),
                'image' => new \Barberry\PostedFile(Test\Data::gif1x1(), 'test.gif')
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function p($tbs = null, $tempPath = null) {
        return new OpenOfficeTemplate(
            $tbs ?: Test\Stub::create('clsTinyButStrong'),
            $tempPath ?: ''
        );
    }
}
