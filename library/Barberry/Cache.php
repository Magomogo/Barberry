<?php
namespace Barberry;

use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamInterface;

class Cache {

    private Filesystem $filesystem;
    private Destination $destination;

    public function __construct(Filesystem $filesystem, Destination $destination) {
        $this->filesystem = $filesystem;
        $this->destination = $destination;
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
        $path  = $this->destination->generate($id);
        if ($this->directoryExists($path)) {
            $this->filesystem->deleteDirectory($path);
        }
    }

    private function writeToFilesystem($streamOrContent, $filePath): void
    {
        if ($streamOrContent instanceof StreamInterface) {
            $resource = tmpfile();
            if ($resource === false) {
                throw new Cache\Exception('Unable to create temporary file');
            }
            $streamOrContent->rewind();
            while (!$streamOrContent->eof()) {
                $chunk = $streamOrContent->read(8192);
                fwrite($resource, $chunk);
            }
            $this->filesystem->writeStream($filePath, $resource, ['visibility' => 'public']);
            fclose($resource);
        } else {
            $this->filesystem->write($filePath, $streamOrContent, ['visibility' => 'public']);
        }
    }

    private function filePath(Request $request): string
    {
        $file = self::directoryByRequest($request);

        if ($this->filesystem->fileExists($file)) {
            return $file;
        }

        return $this->destination->generate($request->id) . $file;
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
