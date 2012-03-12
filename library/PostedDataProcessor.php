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
        if (count($request) && $this->canBeParsed($spec)) {
            $file = $this->parserFactory->{self::parserFactoryMethod($spec)}()
                    ->parse($file, $request);
        }

        return $file;
    }

    private function canBeParsed($spec) {
        return method_exists($this->parserFactory, self::parserFactoryMethod($spec));
    }

    private static function originalFileExtension($spec) {
        return strpos($spec['name'], '.') !== false ?
                substr($spec['name'], strrpos($spec['name'], '.') + 1) : '';
    }

    private static function parserFactoryMethod($spec) {
        return strtolower(self::originalFileExtension($spec)) . 'Parser';
    }
}
