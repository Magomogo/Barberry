<?php
namespace Barberry;

use Barberry\nonlinear;
use Barberry\fs;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

class Cache {

    private $path;

    public function __construct($path) {
        $this->path = fs\als($path);
    }

    /**
     * @param StreamInterface|string $streamOrContent
     * @param Request $request
     * @return string
     * @throws Cache\Exception
     */
    public function save($streamOrContent, Request $request): string
    {
        $this->writeToFilesystem($streamOrContent, $this->filePath($request));

        return $request->id;
    }

    public function invalidate($id): void
    {
        $dir = $this->path . nonlinear\generateDestination($id);
        if (is_dir($dir)) {
            fs\rmDirRecursive($dir);
        }
    }

    private function writeToFilesystem($streamOrContent, $filePath): void
    {
        if (!is_dir($d = dirname($filePath))) {
            if (!mkdir($d, 0777, true) && !is_dir($d)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $d));
            }
        }

        if ($streamOrContent instanceof StreamInterface) {
            Utils::copyToStream(
                $streamOrContent,
                Utils::streamFor(
                    Utils::tryFopen($filePath, 'w+')
                )
            );
        } else {
            $bytes = @file_put_contents($filePath, $streamOrContent);
            if ($bytes === false) {
                $msg = error_get_last();
                throw new Cache\Exception($filePath, isset($msg['message']) ? $msg['message'] : '');
            }
        }
    }

    private function filePath(Request $request): string
    {
        $file = self::directoryByRequest($request);

        if (is_file($f = $this->path . $file)) {
            return $f;
        }

        return $this->path . nonlinear\generateDestination($request->id) . $file;
    }

    private static function directoryByRequest(Request $request): string
    {
        return implode(
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
