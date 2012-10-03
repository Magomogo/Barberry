<?php
namespace Barberry\Parser;
use Barberry\Test;

class OpenOfficeTemplateIntegrationTest extends \PHPUnit_Framework_TestCase {

    const PARSED_DOCUMENT_SIZE = 8369;

    public function testParsesSpreadSheet() {
        $result = self::p()->parse(
            Test\Data::otsTemplate(),
            array(
                'd' => array(
                    array('id' => 1),
                    array('id' => 2),
                ),
                'message' => 'Maxim was here'
            )
        );
        $this->assertEquals(self::PARSED_DOCUMENT_SIZE, strlen($result));
    }

//--------------------------------------------------------------------------------------------------

    private static function p() {
        return new OpenOfficeTemplate(new \clsTinyButStrong, '/tmp/');
    }
}
