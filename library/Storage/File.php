<?php

class Storage_File implements Storage_Interface {

    private $path;

    public function __construct($path) {
        $this->path = rtrim($path, '/').'/';
    }

    public function getById($id) {
        $filePath = $this->filePathById($id);

        if(file_exists($filePath)) {
                return file_get_contents($filePath);
        } else {
                throw new Storage_Exception($filePath);
        }
    }

    public function save($content) {
        $id = $this->generateUniqueId();
        $filePath = $this->filePathById($id);

        file_put_contents($filePath, $content);

        if(file_exists($filePath)) {
            return $id;
        }
        throw new Storage_Exception($filePath);
    }

    public function delete($id) {
        $filePath = $this->filePathById($id);

        if(file_exists($filePath)) {
                unlink($filePath);
        } else {
                throw new Storage_Exception($filePath);
        }
    }

//--------------------------------------------------------------------------------------------------

    private function generateUniqueId() {
        return basename(tempnam($this->path, ''));
    }

    private function filePathById($id) {
        return $this->path.$id;
    }
}
