<?php
namespace Barberry\Storage;

use Barberry\ContentType;
use Barberry\Destination;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamInterface;

class File implements StorageInterface
{
    private Filesystem $filesystem;
    private Destination $destination;

    public function __construct(Filesystem $filesystem, Destination $destination)
    {
        $this->filesystem = $filesystem;
        $this->destination = $destination;
    }

    /**
     * @param string $id
     * @return StreamInterface
     * @throws NotFoundException
     */
    public function getById(string $id): StreamInterface
    {
        $path = $this->filePathById($id);
        if ($this->filesystem->fileExists($path)){
            return Utils::streamFor($this->filesystem->readStream($path));
        }

        throw new NotFoundException($id);
    }

    /**
     * @param string $id
     * @return ContentType
     * @throws ContentType\Exception
     */
    public function getContentTypeById(string $id): ContentType
    {
        $path = $this->filePathById($id);

        $content = $this->filesystem->read($path);

        return ContentType::byString($content);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string content id
     */
    public function save(UploadedFile $uploadedFile)
    {
        do {
            $id = $this->generateUniqueId();
            $path = $this->filePathById($id);
        } while ($this->filesystem->fileExists($path));

        $this->filesystem->createDirectory(dirname($path));

        $stream = $uploadedFile->getStream();

        $this->filesystem->writeStream($path, $stream->detach());

        return $id;
    }

    /**
     * @param string $id
     * @throws NotFoundException
     */
    public function delete(string $id)
    {
        $filePath = $this->filePathById($id);

        if ($this->filesystem->fileExists($filePath)) {
            $this->filesystem->delete($filePath);
            return;
        }

        throw new NotFoundException($filePath);
    }

    /**
     * @param $id
     * @return string
     */
    private function filePathById($id)
    {
        if ($this->filesystem->fileExists($id)) {
            return $id;
        }

        return $this->destination->generate($id) . $id;
    }

    private function generateUniqueId(): string
    {
        if (extension_loaded('openssl')) {
            $bytes = openssl_random_pseudo_bytes(10);
            return bin2hex($bytes);
        }
        return uniqid('', true);
    }
}
