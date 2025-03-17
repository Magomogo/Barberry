<?php

namespace Barberry;

class Destination
{
    private int $depth;
    private int $len;

    public function __construct(int $depth = 3, int $len = 2) {
        $this->depth = $depth;
        $this->len = $len;
    }

    public function generate(string $hash): string
    {
        $start = 0;
        $d = $this->depth;
        $dir = [];

        while ($d-- > 0) {
            $dir[] = substr($hash, $start, $this->len);
            $start += $this->len;
        }
        return self::als(implode(DIRECTORY_SEPARATOR, $dir));
    }

    private static function als($path): string
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
