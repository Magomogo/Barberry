<?php

class ContentType {
    private static $extensionMap = array(
        'jpg' => array('image/jpeg'),
        'jpeg' => array('image/jpeg'),
        'gif' => array('image/gif'),
        'json' => array('application/json'),
        'php' => array('text/x-php', 'text/php'),
    );

    private $contentTypeString;

    public static function jpeg() {
        return new self(self::$extensionMap['jpg'][0]);
    }

    public static function gif() {
        return new self(self::$extensionMap['gif'][0]);
    }

    public static function json() {
        return new self(self::$extensionMap['json'][0]);
    }

    public static function createByExtention($ext) {
        if(isset(self::$extensionMap[$ext])) {
            return new self(self::$extensionMap[$ext][0]);
        }
        throw new ContentType_Exception($ext);
    }

    public static function createByContentTypeString($contentTypeString) {
        foreach(self::$extensionMap as $ext=>$contentTypeStringArray) {
            foreach($contentTypeStringArray as $str) {
                if(false !== strpos($contentTypeString, $str)) {
                    return self::createByExtention($ext);
                }
            }
        }
        throw new ContentType_Exception($contentTypeString);
    }

    private function __construct($contentTypeString) {
        $this->contentTypeString = $contentTypeString;
    }

    public function standartExtention() {
        foreach(self::$extensionMap as $ext=>$contentTypeStringArray) {
            if($this->contentTypeString === $contentTypeStringArray[0]) {
                return $ext;
            }
        }
        throw new ContentType_Exception($this->contentTypeString);
    }

    public function __toString() {
        return $this->contentTypeString;
    }
}
