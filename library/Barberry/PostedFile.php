<?php

namespace Barberry;

use GuzzleHttp\Psr7\UploadedFile;

/**
 * @property-read UploadedFile $uploadedFile
 * @property-read string $tmpName
 */
class PostedFile
{
    /**
     * @var UploadedFile
     */
    private $uploadedFile;

    /**
     * @var string
     */
    private $tmpName;

    /**
     * @param UploadedFile $uploadedFile
     * @param string $tmpName
     */
    public function __construct(UploadedFile $uploadedFile, string $tmpName)
    {
        $this->uploadedFile = $uploadedFile;
        $this->tmpName = $tmpName;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }
        trigger_error('Undefined property via __get(): ' . $property, E_USER_NOTICE);
        return null;
    }
}
