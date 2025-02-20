<?php
namespace Barberry;

use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamInterface;

class Cache {

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem) {
        $this->filesystem = $filesystem;
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
        $path  = nonlinear\generateDestination($id);
        if ($this->directoryExists($path)) {
            $this->filesystem->deleteDirectory($path);
        }
    }

    private function writeToFilesystem($streamOrContent, $filePath): void
    {
        $dir = dirname($filePath);
        if (!$this->directoryExists($dir)) {
            $this->filesystem->createDirectory($dir);
        }

        if ($streamOrContent instanceof StreamInterface) {
            $this->filesystem->writeStream($filePath, $streamOrContent->detach());
        } else {
            $this->filesystem->write($filePath, $streamOrContent);
        }
    }

    private function filePath(Request $request): string
    {
        $file = self::directoryByRequest($request);

        if ($this->filesystem->fileExists($file)) {
            return $file;
        }

        return nonlinear\generateDestination($request->id) . $file;
    }

    private static function directoryByRequest(Request $request): string
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array_filter(
                array(
                    $request->group,
                    $request->id . '/' . $request->originalBasename,
                ),
            ),
        );
    }

    private function directoryExists(string $path): bool
    {
        if (method_exists($this->filesystem, 'directoryExists')) {
            return $this->filesystem->directoryExists($path);
        }
        if (method_exists($this->filesystem, 'listContents')) {
            $content = $this->filesystem->listContents($path, false)->toArray();
            return !empty($content);
        }
        return false;
    }

}
