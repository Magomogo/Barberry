<?php

class Request {

    /**
     * @var string
     */
    public $group;

    /**
     * @var string
     */
    public $id;

    /**
     * @var ContentType
     */
    public $contentType;

    /**
     * @var null|string
     */
    public $bin;

    /**
     * @var null|string
     */
    public $postedFilename;

    /**
     * @var string
     */
    public $commandString;

    public function __construct($uri, $postInfo = null) {
        $parts = array_values(array_filter(explode('/', $uri)));
        switch (1) {
            case (count($parts) == 2) && preg_match('@^[a-z]{3}$@i', $parts[0]):
                $this->group = array_shift($parts);
            case count($parts) == 1:
                $this->id = self::extractId($parts[0]);
                $this->commandString = self::extractCommandString($parts[0]);
                $this->contentType = self::extractOutputContentType($parts[0]);
        }
        if (is_array($postInfo)) {
            $this->bin = array_key_exists('content', $postInfo) ? $postInfo['content'] : null;
            $this->postedFilename = array_key_exists('filename', $postInfo) ?
                $postInfo['filename'] : null;
        }
    }

//--------------------------------------------------------------------------------------------------

    private static function extractId($part) {
        if (preg_match('@^([0-9a-z]+)[_\.]?@i', $part, $regs)) {
            return $regs[1];
        }
        return null;
    }

    private static function extractCommandString($uri) {
        if (preg_match('@^[^_]+_(.*)\.[a-z]+@i', $uri, $regs)) {
            return $regs[1];
        }
        return null;
    }

    private static function extractOutputContentType($uri) {
        if (preg_match('@\.([a-z]+)$@i', $uri, $regs)) {
            try {
                return ContentType::byExtention($regs[1]);
            } catch (ContentType_Exception $e) {}
        }
        return null;
    }
}
