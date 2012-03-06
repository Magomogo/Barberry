<?php

class Dispatcher {

    /**
     * @var Storage_Interface
     */
    private $controller;

    /**
     * @var PostedDataProcessor
     */
    private $dataProcessor;

    public function __construct(Controller_Interface $controller,
                                PostedDataProcessor $dataProcessor) {
        $this->controller = $controller;
        $this->dataProcessor = $dataProcessor;
    }

    public function dispatchRequest($uri, array $phpFiles = array(), array $request = array()) {
        $this->controller->requestDispatched(
            self::extractId($uri),
            self::extractOutputContentType($uri),
            $this->dataProcessor->process($phpFiles, $request)
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
