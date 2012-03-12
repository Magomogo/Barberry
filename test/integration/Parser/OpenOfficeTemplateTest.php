<?php

class Integration_Parser_OpenOfficeTemplateTest extends PHPUnit_Framework_TestCase {

    const PARSED_DOCUMENT_SIZE = 8369;

    public function testParsesSpreadSheet() {
        $result = self::p()->parse(
            Test_Data::otsTemplate(),
            array(
                'd' => array(
                    array('id' => 1),
                    array('id' => 2),
                ),
                'message' => 'Maxim was here'
            )
        );
        //file_put_contents(Config::get()->directoryTemp . 'result.ods', $result);
        $this->assertEquals(self::PARSED_DOCUMENT_SIZE, strlen($result));
    }

//--------------------------------------------------------------------------------------------------

    private static function p() {
        return new Parser_OpenOfficeTemplate(new clsTinyButStrong, Config::get()->directoryTemp);
    }
}
