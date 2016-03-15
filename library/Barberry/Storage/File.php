<?php
namespace Barberry\Storage;

use Barberry\fs;
use Barberry\nonlinear;

class File implements StorageInterface {

    private $permanentStoragePath;

    private $baseLen = 10;

    public function __construct($path) {
        $this->permanentStoragePath = fs\als($path);
    }

    /**
     * @param string $id
     * @return string
     * @throws NotFoundException
     */
    public function getById($id) {
        $filePath = $this->filePathById($id);
        $content = false;

        if (is_file($filePath)) {
            $content = file_get_contents($filePath);
        }

        if ($content === false) {
            throw new NotFoundException($filePath);
        }

        return $content;
    }

    /**
     * @param string $content
     * @return string content id
     * @throws WriteException
     */
    public function save($content) {
        do {
            $id = $this->generateUniqueId();
        } while (file_exists($filePath = $this->filePathById($id)));

        mkdir(dirname($filePath), 0777, true);
        if (file_put_contents($filePath, $content) === false) {
            throw new WriteException($id);
        }

        return $id;
    }

    /**
     * @param string $id
     * @throws NotFoundException
     */
    public function delete($id) {
        $filePath = $this->filePathById($id);

        if (is_file($filePath)) {
            unlink($filePath);
        } else {
            throw new NotFoundException($filePath);
        }
    }

    /**
     * @param $id
     * @return string
     */
    private function filePathById($id) {
        if (is_file($f = $this->permanentStoragePath . $id)) {
            return $f;
        }

        return $this->permanentStoragePath . nonlinear\generateDestination($id) . $id;
    }

    private function generateUniqueId()
    {
        if (extension_loaded('openssl')) {
            $bytes = openssl_random_pseudo_bytes($this->baseLen);
            return bin2hex($bytes);
        }
        return $this->baseLen > 10 ? md5(uniqid('', true)) : uniqid('');
    }
}
