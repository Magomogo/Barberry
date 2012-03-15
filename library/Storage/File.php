<?php

class Storage_File implements Storage_Interface {

    private $permanentStoragePath;

    public function __construct($path) {
        $this->permanentStoragePath = rtrim($path, '/') . '/';
    }

    /**
     * @param string $id
     * @return string
     * @throws Storage_NotFoundException
     */
    public function getById($id) {
        $filePath = $this->filePathById($id);

        if (is_file($filePath)) {
            return file_get_contents($filePath);
        }
        else {
            throw new Storage_NotFoundException($filePath);
        }
    }

    /**
     * @param string $content
     * @return string content id
     * @throws Storage_WriteException
     */
    public function save($content) {
        $id = $this->generateUniqueId();
        $filePath = $this->filePathById($id);

        @file_put_contents($filePath, $content);

        if(is_file($filePath)) {
            return $id;
        }
        throw new Storage_WriteException($id);
    }

    /**
     * @param string $id
     * @throws Storage_NotFoundException
     */
    public function delete($id) {
        $filePath = $this->filePathById($id);

        if (is_file($filePath)) {
            unlink($filePath);
        }
        else {
            throw new Storage_NotFoundException($filePath);
        }
    }

//--------------------------------------------------------------------------------------------------

    private function generateUniqueId() {
        $tempFile = tempnam($this->permanentStoragePath, '');
        chmod($tempFile, 0664);
        return basename($tempFile);
    }

    private function filePathById($id) {
        return $this->permanentStoragePath . $id;
    }
}
