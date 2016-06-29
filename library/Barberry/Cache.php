<?php
namespace Barberry;

use Barberry\nonlinear;
use Barberry\fs;

class Cache {

    private $path;

    public function __construct($path) {
        $this->path = fs\als($path);
    }

    public function save($content, Request $request) {
        $this->writeToFilesystem($content, $this->filePath($request));

        return $request->id;
    }

    public function invalidate($id) {
        $dir = $this->path . nonlinear\generateDestination($id);
        if (is_dir($dir)) {
            fs\rmDirRecursive($dir);
        }
    }

    private function writeToFilesystem($content, $filePath) {
        if (!is_dir($d = dirname($filePath))) {
            @mkdir($d, 0777, true);
        }

        $bytes = @file_put_contents($filePath, $content);
        if ($bytes === false) {
            $msg = error_get_last();
            throw new Cache\Exception($filePath, isset($msg['message']) ? $msg['message'] : '');
        }
    }

    private function filePath(Request $request) {
        $file = self::directoryByRequest($request);

        if (is_file($f = $this->path . $file)) {
            return $f;
        }

        return $this->path . nonlinear\generateDestination($request->id) . $file;
    }

    private static function directoryByRequest(Request $request) {
        return join(
            DIRECTORY_SEPARATOR,
            array_filter(
                array(
                    $request->group,
                    $request->id . '/' . $request->originalBasename
                )
            )
        );
    }

}
