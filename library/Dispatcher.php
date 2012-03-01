<?php

class Dispatcher {

    /**
     * @var Storage_Interface
     */
    private $storage;


    public function __construct(Storage_Interface $storage) {
        $this->storage = $storage;
    }

    public function dispatch($uri, $requestArray) {
        return new Controller(
            $this->storage,
            self::extractId($uri),
            self::extractOutputContentType($uri)
        );
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
            return ContentType::createByExtention($regs[1]);
        }
        return null;
    }
}
