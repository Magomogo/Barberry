<?php

class ContentType {
    private static $extensionMap = array(
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'json' => 'application/json',
        'php' => 'text/x-php',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'txt' => 'text/plain',
        'xls' => 'application/vnd.ms-excel',
        'doc' => 'application/vnd.ms-word',
        'pdf' => 'application/pdf',
    );

    private $contentTypeString;

    public static function jpeg() {
        return new self(self::$extensionMap['jpg']);
    }

    public static function gif() {
        return new self(self::$extensionMap['gif']);
    }

    public static function json() {
        return new self(self::$extensionMap['json']);
    }

    public static function ott() {
        return new self(self::$extensionMap['ott']);
    }

    public static function ots() {
        return new self(self::$extensionMap['ots']);
    }

    public static function xls() {
        return new self(self::$extensionMap['xls']);
    }

    public static function doc() {
        return new self(self::$extensionMap['doc']);
    }

    public static function odt() {
        return new self(self::$extensionMap['odt']);
    }

    public static function ods() {
        return new self(self::$extensionMap['ods']);
    }

    public static function pdf() {
        return new self(self::$extensionMap['pdf']);
    }

    public static function byExtention($ext) {
        if(isset(self::$extensionMap[$ext])) {
            return new self(self::$extensionMap[$ext]);
        }
        throw new ContentType_Exception($ext);
    }

    public static function byString($content) {
        $contentTypeString = self::contentTypeString($content);

        $ext = array_search($contentTypeString, self::$extensionMap);

        if(false !== $ext) {
            return self::byExtention($ext);
        }
        throw new ContentType_Exception($contentTypeString);
    }

    private function __construct($contentTypeString) {
        $this->contentTypeString = $contentTypeString;
    }

    public function standartExtention() {
        foreach(self::$extensionMap as $ext=>$contentTypeStringArray) {
            if($this->contentTypeString === $contentTypeStringArray) {
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
        $finfo = new finfo(
            FILEINFO_MIME ^ FILEINFO_MIME_ENCODING,
            APPLICATION_PATH . '/scripts/magic.mime.mgc'
        );
        return $finfo->buffer($content);
    }
}
