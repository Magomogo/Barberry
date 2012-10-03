<?php
namespace Barberry;
use Barberry\ContentType;

/**
 * @property-read string $originalBasename
 * @property-read string $id
 * @property-read null|ContentType $contentType
 * @property-read null|string $group
 * @property-read null|string $bin
 * @property-read null|string $postedFilename
 * @property-read null|string $commandString
 */
class Request {

    private $_originalBasename;

    /**
     * @var null|string
     */
    private $_group;

    /**
     * @var string
     */
    private $_id;

    /**
     * @var null|ContentType
     */
    private $_contentType;

    /**
     * @var null|string
     */
    private $_bin;

    /**
     * @var null|string
     */
    private $_postedFilename;

    /**
     * @var null|string
     */
    private $_commandString;

    public function __construct($uri, $postInfo = null) {
        $this->parseUri($uri);
        $this->keepPost($postInfo);
        $this->originalBasename =
            trim($this->group ? substr($uri, strlen($this->group) + 1) : $uri, '/');
    }

    public function defineContentType(ContentType $c) {
        $this->contentType = $c;
    }

    public function __get($property) {
        if (property_exists($this, '_' . $property)) {
            return $this->{'_' . $property};
        }
        trigger_error('Undefined property via __get(): ' . $property, E_USER_NOTICE);
        return null;
    }

//--------------------------------------------------------------------------------------------------

    private function keepPost($postInfo) {
        if (is_array($postInfo)) {
            $this->bin = array_key_exists('content', $postInfo) ? $postInfo['content'] : null;
            $this->postedFilename = array_key_exists('filename', $postInfo) ?
                    $postInfo['filename'] : null;
        }
    }

    private function parseUri($uri) {
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
    }

    private static function extractId($part) {
        if (preg_match('@^([0-9a-z]+)[_\.]?@i', $part, $regs)) {
            return $regs[1];
        }
        return null;
    }

    private static function extractCommandString($uri) {
        if (preg_match('@^[^_]+_([^.]*)\.?[a-z]*@i', $uri, $regs)) {
            return $regs[1];
        }
        return null;
    }

    private static function extractOutputContentType($uri) {
        if (preg_match('@\.([a-z]+)$@i', $uri, $regs)) {
            try {
                return ContentType::byExtention($regs[1]);
            } catch (ContentType\Exception $e) {}
        }
        return null;
    }
}
