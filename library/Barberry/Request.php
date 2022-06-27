<?php

namespace Barberry;

/**
 * @property-read string $originalBasename
 * @property-read string $id
 * @property-read null|ContentType $contentType
 * @property-read null|string $group
 * @property-read null|string $commandString
 * @property-read PostedFile|null $postedFile
 */
class Request
{
    private $originalBasename;

    /**
     * @var null|string
     */
    private $group;

    /**
     * @var string
     */
    private $id;

    /**
     * @var null|ContentType
     */
    private $contentType;

    /**
     * @var null|string
     */
    private $commandString;

    /**
     * @var PostedFile|null
     */
    private $postedFile;

    public function __construct($uri, PostedFile $postedFile = null)
    {
        $this->parseUri($uri);
        $this->postedFile = $postedFile;
    }

    public function defineContentType(ContentType $c)
    {
        $this->contentType = $c;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }
        trigger_error('Undefined property via __get(): ' . $property, E_USER_NOTICE);
        return null;
    }

    private function parseUri($uri)
    {
        $parts = array_values(array_filter(explode('/', $uri)));
        switch (1) {
            case (count($parts) == 2) && preg_match('@^[a-z]{3}$@i', $parts[0]):
                $this->group = array_shift($parts);
            // TRICKY: no break.
            case count($parts) == 1:
                $this->id = self::extractId($parts[0]);
                $this->commandString = self::extractCommandString($parts[0]);
                $this->contentType = self::extractOutputContentType($parts[0]);
        }

        $this->originalBasename =
            trim($this->group ? substr($uri, strlen($this->group) + 1) : $uri, '/');
    }

    private static function extractId($part)
    {
        if (preg_match('@^([0-9a-z]+)[_\.]?@i', $part, $regs)) {
            return $regs[1];
        }
        return null;
    }

    private static function extractCommandString($uri)
    {
        if (preg_match('@^[^_]+_([^.]*)\.?[a-z]*@i', $uri, $regs)) {
            return $regs[1];
        }
        return '';
    }

    private static function extractOutputContentType($uri)
    {
        if (preg_match('@\.([a-z0-9]+)$@i', $uri, $regs)) {
            try {
                return ContentType::byExtention($regs[1]);
            } catch (ContentType\Exception $e) {}
        }
        return null;
    }
}
