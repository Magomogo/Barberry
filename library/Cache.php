<?php

class Cache {

    private $path;

    public function __construct($path) {
        $this->path = rtrim($path,'/').'/';
    }

    public function save($content, $uri) {
        if(!is_dir($this->fileDirPath($uri))) {
            mkdir($this->fileDirPath($uri), 0777, true);
        }

        file_put_contents($this->filePath($uri), $content);

        if(false == file_exists($this->filePath($uri))) {
            throw new Cache_Exception($this->filePath($uri));
        }
    }

//--------------------------------------------------------------------------------------------------

    public function filePath($uri) {
        return $this->path.ltrim($uri,'/');
    }

    private function fileDirPath($uri) {
        return dirname($this->path.ltrim($uri,'/'));
    }
}
