<?php

class GETMethodTest extends PHPUnit_Framework_TestCase {

    public function testEmptyGetReturns404Error() {
        $ch = curl_init('http://' . Config::get()->httpHost);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $this->assertEquals(404, $httpCode, $error);
    }
}

