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

    private $depth = 3;

    private $len = 2;

    private $basePath;

    private $baseLen = 10;

    private function __construct($basePath, $base)
    {
        $this->basePath = self::als($basePath);
        $this->base = $base;
    }

    private function __clone()
    {
    }

    /**
     * @param string $basePath
     * @param string|null $base data for calculating non-linear destination, created new if not passed
     * @return static
     */
    public static function factory($basePath, $base = null)
    {
        return new static($basePath, $base);
    }

    /**
     * Set up depth of non-linear destination
     * @param int $depth
     * @return $this
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Max length  non-linear structure item
     * @param int $len in characters
     * @return $this
     */
    public function setLen($len)
    {
        $this->len = $len;
        return $this;
    }

    /**
     * Max length for base
     * @param int $baseLen in bytes
     * @return $this
     */
    public function setBaseLen($baseLen)
    {
        $this->baseLen = $baseLen;
        return $this;
    }

    /**
     * Generate and create destination path, if not exists
     *
     * @param string $d destination path
     * @throws WriteException
     */
    private function make($d)
    {
        $created = mkdir($d, 0777, true);
        if ($created === false) {
            $error = error_get_last();
            throw new WriteException($this->base, $error['message']);
        }
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
        return $this->basePath . self::als(implode(DIRECTORY_SEPARATOR, $dir));
    }

    public function getBase() {
        if (isset($this->base)) {
            return $this->base;
        }
        do {
            $this->base = $this->generateUniqueId();
            $d = $this->generate();
        } while (file_exists($d . $this->base));

        $this->make($d);

        return $this->base;
    }

    protected function generateHash()
    {
        return $this->base;
    }

    private function generateUniqueId()
    {
        if (extension_loaded('openssl')) {
            $bytes = openssl_random_pseudo_bytes($this->baseLen);
            return bin2hex($bytes);
        }
        return $this->baseLen > 10 ? md5(uniqid('', true)) : uniqid('');
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