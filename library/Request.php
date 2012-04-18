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

    public function __construct($uri, $bin = null) {
        $this->id = self::extractId($uri);
        $this->contentType = self::extractOutputContentType($uri);
        $this->bin = $bin;
    }

//--------------------------------------------------------------------------------------------------

    private static function extractId($uri) {
        if (preg_match('/^\/?([0-9a-z]+)[_\.]/i', $uri, $regs)) {
            return $regs[1];
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

}
