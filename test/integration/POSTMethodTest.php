<?php

class POSTMethodTest extends PHPUnit_Framework_TestCase {

    public function test_Post_1x1_GifImage_Save_GifImage_In_Storage() {
        $handle = $this->post(
            'http://' . Config::get()->httpHost,
            Test_Data::gif1x1()
        );

        $response = curl_exec($handle);

        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        $this->assertEquals(201, $httpCode, $error);
        $this->assertRegExp('{"id":"\w+"}', $response);
        $this->assertEquals(Test_Data::gif1x1(), self::storage()->getById(json_decode($response)->id));

        # clean
        self::storage()->delete(json_decode($response)->id);
    }

    /**
     * @return Storage_File
     */
    private static function storage() {
        return Resources::get()->storage();
    }

    /**
     *
     * http://stackoverflow.com/questions/3085990/post-a-file-string-using-curl-in-php
     *
     * @param $url
     * @param $fileContent
     * @return resource
     */
    private function post($url, $fileContent) {
        $fileName = "file";
        $fileInfo = new finfo(FILEINFO_MIME);

        $delimiter = '-------------' . uniqid();

        $data = "--" . $delimiter .
                "\r\n";
        $data .= 'Content-Disposition: form-data; name="' . $fileName . '";' .
                 ' filename="' . $fileName . '"' .
                "\r\n";
        $data .= 'Content-Type: ' . $fileInfo->buffer($fileContent) .
                "\r\n";
        # this end line must be here to indicate end of headers
        $data .= "\r\n";
        // the file itself (note: there's no encoding of any kind)
        $data .= $fileContent .
                "\r\n";

        // last delimiter
        $data .= "--" . $delimiter .
                "--\r\n";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $handle,
            CURLOPT_HTTPHEADER,
            array (
                'Content-Type: multipart/form-data; boundary=' . $delimiter,
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);

        return $handle;
    }
}