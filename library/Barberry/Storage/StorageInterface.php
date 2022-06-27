<?php

namespace Barberry\Storage;

use Barberry\ContentType;
use GuzzleHttp\Psr7\UploadedFile;
use Psr\Http\Message\StreamInterface;

interface StorageInterface
{

    /**
     * @param string $id
     * @return StreamInterface
     * @throws NotFoundException
     */
    public function getById(string $id): StreamInterface;

    /**
     * @param string $id
     * @return ContentType
     * @throws ContentType\Exception
     */
    public function getContentTypeById(string $id): ContentType;

    /**
     * @param string $id
     * @throws NotFoundException
     */
    public function delete(string $id);

    /**
     * @param UploadedFile $uploadedFile
     * @return string content id
     */
    public function save(UploadedFile $uploadedFile);
}
