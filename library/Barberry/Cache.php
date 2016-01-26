<?php
namespace Barberry;

use Barberry\Storage\File\NonLinearDestination;

class Cache {

    const FILE_MODE = 0664;
    const DIR_MODE = 0775;

    private $path;

    public function __construct($path) {
        $this->path = rtrim($path,'/') . '/';
    }

    public function save($content, Request $request) {
        $this->writeToFilesystem($content, $this->filePath($request));
        $this->assertFileWasWritten($this->filePath($request));

        return $request->id;
    }

    public function invalidate($id) {
        $dir = $this->path . $id;
        if (is_dir($dir)) {
            self::rmDirRecursive($dir);
        }
    }

    protected function writeToFilesystem($content, $filePath) {
        if (!is_dir($d = dirname($filePath))) {
            mkdir($d, self::DIR_MODE, true);
        }

        file_put_contents($filePath, $content);
        chmod($filePath, self::FILE_MODE);
    }

    private function assertFileWasWritten($filePath) {
        if (!is_file($filePath)) {
            throw new Cache\Exception($filePath);
        }
    }

    private function filePath(Request $request) {
        $file = self::directoryByRequest($request);
        if (is_file($f = $this->path . $file)) {
            return $f;
        }

        $path = NonLinearDestination::factory($request->id)->generate();

        return $this->path . $path . $file;
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
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);

        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') continue;
            if (!self::rmDirRecursive($dir . '/' . $file)) {
                chmod($dir . '/' . $file, self::FILE_MODE);
                if (!self::rmDirRecursive($dir . '/' . $file)) return false;
            };
        }

        return rmdir($dir);
    }
}
