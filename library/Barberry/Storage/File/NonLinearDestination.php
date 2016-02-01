<?php
/**
 * @author lion
 */

namespace Barberry\Storage\File;

use Barberry\Storage\WriteException;

class NonLinearDestination
{
    /** @var string */
    private $base;

    private $depth;

    private $len = 2;

    private function __construct($base, $depth)
    {
        $this->base = $base;
        $this->depth = $depth;
    }

    private function __clone()
    {
    }

    /**
     * @param string $base
     * @param int $depth
     * @return static
     */
    public static function factory($base, $depth = 3)
    {
        return new static($base, $depth);
    }

    /**
     * Generate and create destination path, if not exists
     *
     * @param string $basePath
     * @param int $mode of destination path
     * @return string destination path
     * @throws WriteException
     */
    public function make($basePath, $mode = 0775)
    {
        $destination = $this->generate();

        if (!is_dir($d = self::als($basePath) . $destination)) {
            $created = mkdir($d, $mode, true);
            if ($created === false) {
                $error = error_get_last();
                throw new WriteException($this->base, $error['message']);
            }
        }

        return $d;
    }

    public function generate()
    {
        $hash = $this->generateHash();
        $start = 0;
        $d = $this->depth;
        $dir = array();

        while ($d-- > 0) {
            $dir[] = substr($hash, $start, $this->len);
            $start += $this->len;
        }
        return self::als(implode(DIRECTORY_SEPARATOR, $dir));
    }

    protected function generateHash()
    {
        return $this->base;
    }

    /**
     * Append last slash
     *
     * @param string $path
     * @return string
     */
    public static function als($path)
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

}