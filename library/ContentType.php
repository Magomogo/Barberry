<?php

class ContentType {
    private static $extensionMap = array(
        'jpg' => array('image/jpeg'),
        'jpeg' => array('image/jpeg'),
        'gif' => array('image/gif'),
        'json' => array('application/json'),
        'php' => array('text/x-php', 'text/php'),
        'ott' => array('application/vnd.oasis.opendocument.text-template'),
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

    public static function ott() {
        return new self(self::$extensionMap['ott'][0]);
    }

    public static function byExtention($ext) {
        if(isset(self::$extensionMap[$ext])) {
            return new self(self::$extensionMap[$ext][0]);
        }
        throw new ContentType_Exception($ext);
    }

    public static function byString($content) {
        $contentTypeString = self::contentTypeString($content);

        foreach(self::$extensionMap as $ext=>$contentTypeStringArray) {
            foreach($contentTypeStringArray as $str) {
                if(false !== strpos($contentTypeString, $str)) {
                    return self::byExtention($ext);
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

//--------------------------------------------------------------------------------------------------

    private static function contentTypeString($content) {
        $finfo = new finfo(FILEINFO_MIME, APPLICATION_PATH . '/scripts/magic.mgc');
        return $finfo->buffer($content);
    }
}
