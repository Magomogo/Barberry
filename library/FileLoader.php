<?php

class FileLoader {

    public function process(array $phpFiles) {
        foreach ($phpFiles  as $spec) {
            if (($spec['error'] == UPLOAD_ERR_OK) && ($spec['size'] > 0)) {
                return $this->readTempFile($spec['tmp_name']);
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
}
