<?php

class DirectAccessToCacheTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        Resources::get()->cache()->save('123', new Request('/' . __CLASS__ . '.txt'));
    }

    protected function tearDown() {
        unlink(Config::get()->directoryCache . __CLASS__ . '/' .__CLASS__ . '.txt');
        rmdir(Config::get()->directoryCache . __CLASS__);
    }

    public function testAvailableByRewriteRules_RestrictedByDirectRequest() {
        $handle = self::get('http://' . Config::get()->httpHost . '/' . __CLASS__ . '.txt');
        curl_exec($handle);
        $this->assertEquals(200, curl_getinfo($handle, CURLINFO_HTTP_CODE));
        curl_close($handle);

        $handle = self::get('http://' . Config::get()->httpHost . '/cache/' . __CLASS__ . '/' . __CLASS__. '.txt');
        curl_exec($handle);
        $this->assertEquals(403, curl_getinfo($handle, CURLINFO_HTTP_CODE));
        curl_close($handle);

    }

//--------------------------------------------------------------------------------------------------

    private static function get($url) {
        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        return $handle;
    }

}
