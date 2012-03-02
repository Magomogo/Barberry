<?php

class Dispatcher {

    /**
     * @var Storage_Interface
     */
    private $controller;

    /**
     * @var FileLoader
     */
    private $fileLoader;

    public function __construct(Controller_Interface $controller, $fileLoader) {
        $this->controller = $controller;
        $this->fileLoader = $fileLoader;
    }

    public function dispatchRequest($uri, array $phpFiles = array()) {
        $this->controller->requestDispatched(
            self::extractId($uri),
            self::extractOutputContentType($uri),
            $this->fileLoader->process($phpFiles)
        );
        return $this->controller;
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
