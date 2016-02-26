<?php
namespace Barberry;

use function Barberry\file\als;

class Cache {

    private $path;

    public function __construct($path) {
        $this->path = als($path);
        set_error_handler(array($this, 'errorHandler'));
    }

    public function save($content, Request $request) {
        $this->writeToFilesystem($content, $this->filePath($request));

        return $request->id;
    }

    public function invalidate($id) {
        $dir = $this->path . $id;
        if (is_dir($dir)) {
            self::rmDirRecursive($dir);
        }
    }

    private function writeToFilesystem($content, $filePath) {
        if (!is_dir($d = dirname($filePath))) {
            mkdir($d, 0777, true);
        }

        file_put_contents($filePath, $content);
    }

    private function filePath(Request $request) {
        $file = self::directoryByRequest($request);

        if (is_file($f = $this->path . $file)) {
            return $f;
        }

        return $this->path . destination\nonlinear\generate($request->id) . $file;
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

    private static function rmDirRecursive($dir) {
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!self::rmDirRecursive($dir . '/' . $file)) {
                if (!self::rmDirRecursive($dir . '/' . $file)) return false;
            };
        }

        return rmdir($dir);
    }

    public function errorHandler($errNo, $errStr, $errFile, $errLine, $errContext)
    {
        if (!array_key_exists('filePath', $errContext)) {
            return false;
        }
        throw new Cache\Exception($errContext['filePath'], $errStr);
    }
}
