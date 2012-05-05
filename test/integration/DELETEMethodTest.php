<?php

class DELETEMethodTest extends PHPUnit_Framework_TestCase {

    public function testDeletesCachedFilesByDocumentId() {
        $id = self::createDocumentAndRequestIt();

        $handle = self::get('http://' . Config::get()->httpHost . '/' . $id);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_exec($handle);
        curl_close($handle);

        $this->assertFalse(
            is_file(Config::get()->directoryCache. "$id/$id.gif")
        );

        $this->setExpectedException('Storage_NotFoundException');
        clearstatcache();
        $this->storage()->getById($id);
    }

    private static function createDocumentAndRequestIt() {
        $id = self::storage()->save(Test_Data::gif1x1());
        $handle = self::get('http://' . Config::get()->httpHost . '/' . $id);
        curl_exec($handle);
        curl_close($handle);
        return $id;
    }

    /**
     * @return Storage_Interface
     */
    private static function storage() {
        return Resources::get()->storage();
    }

    private static function get($url) {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        return $handle;
    }
}