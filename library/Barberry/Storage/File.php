<?php
namespace Barberry\Storage;

class File implements StorageInterface {

    private $permanentStoragePath;

    public function __construct($path) {
        $this->permanentStoragePath = rtrim($path, '/') . '/';
    }

    /**
     * @param string $id
     * @return string
     * @throws NotFoundException
     */
    public function getById($id) {
        $filePath = $this->filePathById($id);

        if (is_file($filePath)) {
            return file_get_contents($filePath);
        }
        else {
            throw new NotFoundException($filePath);
        }
    }

    /**
     * @param string $content
     * @return string content id
     * @throws WriteException
     */
    public function save($content) {
        $id = $this->generateUniqueId();
        $filePath = $this->filePathById($id);

        @file_put_contents($filePath, $content);

        if(is_file($filePath)) {
            return $id;
        }
        throw new WriteException($id);
    }

    /**
     * @param string $id
     * @throws NotFoundException
     */
    public function delete($id) {
        $filePath = $this->filePathById($id);

        if (is_file($filePath)) {
            unlink($filePath);
        }
        else {
            throw new NotFoundException($filePath);
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
