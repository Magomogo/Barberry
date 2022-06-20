<?php

namespace Barberry\Storage;

use Barberry\ContentType;
use GuzzleHttp\Psr7\UploadedFile;
use Psr\Http\Message\StreamInterface;

interface StorageInterface {

    /**
     * @param string $id
     * @return string
     * @throws NotFoundException
     */
    public function getById($id);

    /**
     * @param string $id
     * @return ContentType
     * @throws ContentType\Exception
     */
    public function getContentTypeById($id);

    /**
     * @param string $id
     * @throws NotFoundException
     */
    public function delete($id);

    /**
     * @param UploadedFile $uploadedFile
     * @return string content id
     */
    public function save(UploadedFile $uploadedFile);
}
