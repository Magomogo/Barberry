<?php

class PostedDataProcessor {

    private $parserFactory;

    public function __construct($parserFactory) {
        $this->parserFactory = $parserFactory;
    }

    public function process(array $phpFiles, array $request = array()) {
        foreach ($phpFiles  as $spec) {
            if (($spec['error'] == UPLOAD_ERR_OK) && ($spec['size'] > 0)) {
                return $this->goodUploadedFile($spec, $request);
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

    private function goodUploadedFile($spec, array $request) {
        $file = $this->readTempFile($spec['tmp_name']);

        $parserFactoryMethod = self::parserFactoryMethod($file);
        if (count($request) && method_exists($this->parserFactory, $parserFactoryMethod)) {
            $file = $this->parserFactory->$parserFactoryMethod()->parse($file, $request);
        }

        return $file;
    }

    private static function parserFactoryMethod($file) {
        return ContentType::byString($file)->standartExtention() . 'Parser';
    }
}
