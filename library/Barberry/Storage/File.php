<?php
namespace Barberry\Storage;

use Barberry\ContentType;
use Barberry\fs;
use Barberry\nonlinear;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

class File implements StorageInterface
{
    private $permanentStoragePath;

    private $baseLen = 10;

    public function __construct($path)
    {
        $this->permanentStoragePath = fs\als($path);
    }

    /**
     * @param string $id
     * @return StreamInterface
     * @throws NotFoundException
     */
    public function getById(string $id): StreamInterface
    {
        $filePath = $this->filePathById($id);

        if (is_file($filePath)) {
            return Utils::streamFor(
                Utils::tryFopen($filePath, 'rb')
            );
        }

        throw new NotFoundException($filePath);
    }

    /**
     * @param string $id
     * @return ContentType
     * @throws ContentType\Exception
     */
    public function getContentTypeById(string $id): ContentType
    {
        return ContentType::byFilename(
            $this->filePathById($id)
        );
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string content id
     */
    public function save(UploadedFile $uploadedFile)
    {
        do {
            $id = $this->generateUniqueId();
        } while (file_exists($filePath = $this->filePathById($id)));

        if (!is_dir(dirname($filePath))) {
            if (!mkdir($directory = dirname($filePath), 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        $uploadedFile->moveTo($filePath);

        return $id;
    }

    /**
     * @param string $id
     * @throws NotFoundException
     */
    public function delete(string $id)
    {
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
    private function filePathById($id)
    {
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
