<?php

class Request {

    /**
     * @var string
     */
    public $id;

    /**
     * @var ContentType
     */
    public $contentType;

    /**
     * @var null, string
     */
    public $bin;

    /**
     * @var string
     */
    public $commandString;

    public function __construct($uri, $bin = null) {
        $this->id = self::extractId($uri);
        $this->commandString = self::extractCommandString($uri);
        $this->contentType = self::extractOutputContentType($uri);
        $this->bin = $bin;
    }

//--------------------------------------------------------------------------------------------------

    private static function extractId($uri) {
        if (preg_match('/' . self::idRegExp() . '[\/\.]/i', $uri, $regs)) {
            return $regs[1];
        }
        return null;
    }

    private static function extractCommandString($uri) {
        if (preg_match('/' . self::idRegExp() . '[\/](.*)\.[a-z]+/i', $uri, $regs)) {
            return $regs[2];
        }
        return null;
    }

    private static function extractOutputContentType($uri) {
        if (preg_match('/\.([a-z]+)$/i', $uri, $regs)) {
            try {
                return ContentType::byExtention($regs[1]);
            } catch (ContentType_Exception $e) {}
        }
        return null;
    }

    private static function idRegExp() {
        return '^\/?([0-9a-z]+)';
    }
}
