<?php

class PostedDataProcessor {

    private $parserFactory;

    public function __construct($parserFactory) {
        $this->parserFactory = $parserFactory;
    }

    public function process(array $phpFiles, array $request = array()) {
        foreach ($phpFiles  as $spec) {
            if (($spec['error'] == UPLOAD_ERR_OK) && ($spec['size'] > 0)) {
                return $this->goodUploadedFile($spec, $request, $spec['name']);
            }
        }
        return null;
    }

//--------------------------------------------------------------------------------------------------

    protected function readTempFile($filepath) {
        if (is_uploaded_file($filepath)) {
            return file_get_contents($filepath);
        }
        return null;
    }

    private function goodUploadedFile($spec, array $request, $filename) {
        $file = $this->readTempFile($spec['tmp_name']);

        if (count($request)) {
            $parserFactoryMethod = self::parserFactoryMethod($file);
            if (method_exists($this->parserFactory, $parserFactoryMethod)) {
                $file = $this->parserFactory->$parserFactoryMethod()->parse($file, $request);
            }
        }

        return array('content' => $file, 'filename' => $filename);
    }

    private static function parserFactoryMethod($file) {
        return ContentType::byString($file)->standartExtention() . 'Parser';
    }
}
