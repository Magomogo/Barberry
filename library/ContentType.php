<?php

class ContentType {
    private static $extensionMap = array(
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'png'  => 'image/png',

        'flv'  => 'video/x-flv',
        'webm' => 'video/webm',
        'wmv'  => 'video/x-ms-wmv',
        'mpg'  => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'avi'  => 'video/x-msvideo',
        'mkv'  => 'video/x-matroska',
        'mp4'  => 'video/mp4',
        'mov'  => 'video/quicktime',
        'qt'   => 'video/quicktime',
        'ogv'  => 'video/ogg',
        '3gp'  => 'video/3gpp',
        '_3gp'  => 'video/3gpp',

        'json' => 'application/json',
        'php'  => 'text/x-php',
        'ott'  => 'application/vnd.oasis.opendocument.text-template',
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'ots'  => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
        'txt'  => 'text/plain',
        'xls'  => 'application/vnd.ms-excel',
        'doc'  => 'application/vnd.ms-word',
        'pdf'  => 'application/pdf',
        'url'  => 'text/url',
    );

    private $contentTypeString;

    public static function __callStatic($method, $args)
    {
        if (isset(self::$extensionMap[$method]))
            return self::byExtention($method);

        throw new Exception("Undefined method ".get_called_class()."->{$method}() called.");
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

    public function isImage() {
        return (preg_match('/^image\\//i', $this->contentTypeString));
    }

    public function isVideo() {
        return (preg_match('/^video\\//i', $this->contentTypeString));
    }

//--------------------------------------------------------------------------------------------------

    private static function contentTypeString($content) {
        if (version_compare(PHP_VERSION, '5.3.11') >= 0) {
            $magic_mime_path = APPLICATION_PATH . '/scripts/v2-magic.mime.mgc';
        } else {
            $magic_mime_path = APPLICATION_PATH . '/scripts/magic.mime.mgc';
        }
        $finfo = new finfo(
            FILEINFO_MIME ^ FILEINFO_MIME_ENCODING,
            $magic_mime_path
        );
        return $finfo->buffer($content);
    }
}
