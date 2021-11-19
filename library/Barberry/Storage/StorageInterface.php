<?php

namespace Barberry\Storage;

use Barberry\ContentType;

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
     * @param string $content
     * @return string content id
     */
    public function save($content);
}
