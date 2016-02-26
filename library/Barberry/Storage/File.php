<?php
namespace Barberry\Storage;

use function Barberry\file\als;
use function Barberry\destination\nonlinear\generate;

class File implements StorageInterface {

    private $permanentStoragePath;

    private $baseLen = 10;

    public function __construct($path) {
        $this->permanentStoragePath = als($path);
        set_error_handler(array($this, 'errorHandler'));
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
        } else {
            throw new NotFoundException($filePath);
        }
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
        file_put_contents($filePath, $content);

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

        return $this->permanentStoragePath . generate($id) . $id;
    }

    private function generateUniqueId()
    {
        if (extension_loaded('openssl')) {
            $bytes = openssl_random_pseudo_bytes($this->baseLen);
            return bin2hex($bytes);
        }
        return $this->baseLen > 10 ? md5(uniqid('', true)) : uniqid('');
    }

    public function errorHandler($errNo, $errStr, $errFile, $errLine, $errContext)
    {
        if (!array_key_exists('id', $errContext)) {
            return false;
        }
        throw new WriteException($errContext['id'], $errStr);
    }
}
