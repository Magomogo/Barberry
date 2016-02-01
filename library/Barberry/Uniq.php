<?php
/**
 * @author lion
 */

namespace Barberry;


class Uniq
{
    public static function id($len = 10)
    {
        $bytes = openssl_random_pseudo_bytes($len);
        return bin2hex($bytes);
    }
}