<?php
namespace Barberry\Filter;
use Barberry\Test;

class OpenOfficeTemplateIntegrationTest extends \PHPUnit_Framework_TestCase {

    const PARSED_DOCUMENT_SIZE = 8369;

    public function testFiltersSpreadSheet() {
        $result = self::p()->filter(
            array(
                'd' => array(
                    array('id' => 1),
                    array('id' => 2),
                ),
                'message' => 'Maxim was here'
            ),
            array('file' => new \Barberry\PostedFile(Test\Data::otsTemplate()))
        );
        $this->assertEquals(self::PARSED_DOCUMENT_SIZE, strlen($result->bin));
    }

//--------------------------------------------------------------------------------------------------

    private static function p() {
        return new OpenOfficeTemplate(new \clsTinyButStrong, '/tmp/');
    }
}
