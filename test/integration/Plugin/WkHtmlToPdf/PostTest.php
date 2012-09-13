<?php

class Plugin_WkHtmlToPdf_PostTest extends PHPUnit_Framework_TestCase {

    public function testMime() {
        $this->assertEquals('text/url', (string)ContentType::byString('http://aaa'));
    }
    public function testIntegration() {
        $handle = $this->post(
            'http://' . Config::get()->httpHost,
            'http://www.ya.ru/',
            'xiag.url'
        );

        $response = curl_exec($handle);

        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);
        $storage_id = json_decode($response)->id;

        $this->assertEquals('http://www.ya.ru/', self::storage()->getById($storage_id));

        $ch = curl_init('http://' . Config::get()->httpHost . '/' . $storage_id . '.jpg');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
        ));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->assertEquals(200, $httpCode, $error);
        $this->assertEquals('image/jpeg', (string)ContentType::byString($response));

        # clean
        self::storage()->delete($storage_id);
    }

    /**
     * @return Storage_File
     */
    private static function storage() {
        return Resources::get()->storage();
    }

    /**
     *
     * copied from POSTMethodTest
     *
     * @param $url
     * @param $fileContent
     * @param $fileName
     * @return resource
     */
    private function post($url, $fileContent, $fileName = "file") {
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

    public function testConverter() {
        $this->assertTrue(class_exists('Plugin_WkHtmlToPdf_Converter'));
        $a = new Plugin_WkHtmlToPdf_Converter(ContentType::jpeg(), '/tmp/');
        $this->assertInstanceOf('Plugin_Interface_Converter', $a);

        $this->assertTrue(class_exists('Plugin_WkHtmlToPdf_Command'));
        $a = new Plugin_WkHtmlToPdf_Command();
        $this->assertInstanceOf('Plugin_Interface_Command', $a);
    }
}