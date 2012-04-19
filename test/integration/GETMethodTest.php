<?php

class GETMethodTest extends PHPUnit_Framework_TestCase {

    public function testEmptyGetReturns404Error() {
        $handle = self::get('http://' . Config::get()->httpHost);
        curl_exec($handle);

        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        $this->assertEquals(404, $httpCode, $error);
    }

    public function test_1x1_GifImage_Get_Returns_GifImage_Content() {
        $id = self::storage()->save(Test_Data::gif1x1());
        $handle = self::get('http://' . Config::get()->httpHost . '/'. $id .'.gif');
        $response = curl_exec($handle);

        $header_size = curl_getinfo($handle,CURLINFO_HEADER_SIZE);
        $body = substr( $response, $header_size);

        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        # clean
        self::storage()->delete($id);
        self::cache()->invalidate(new Request('/' . $id . '.gif'));

        $this->assertEquals(200, $httpCode, $error);
        $this->assertEquals(Test_Data::gif1x1(), $body);
    }

    public function test_1x1_GifImage_Get_Create_Cache_At_First_Request() {
        $id = self::storage()->save(Test_Data::gif1x1());
        $handle = self::get('http://' . Config::get()->httpHost . '/' . $id .'.gif');
        curl_exec($handle);
        curl_close($handle);

        $this->assertEquals(
            Test_Data::gif1x1(),
            file_get_contents(Config::get()->directoryCache. "$id/$id.gif")
        );

        # clean
        self::storage()->delete($id);
        self::cache()->invalidate(new Request('/' . $id . '.gif'));
    }

    /**
     * @return Storage_File
     */
    private static function storage() {
        return new Storage_File(Config::get()->directoryStorage);
    }

    /**
     * @return Cache
     */
    private static function cache() {
        return new Cache(Config::get()->directoryCache);
    }

    private static function get($url) {
        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        return $handle;
    }
}