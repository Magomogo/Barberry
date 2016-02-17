<?php
/**
 * @author lion
 */

namespace Barberry;


class Uniq
{
    public static function id($len = 10)
    {
        if (extension_loaded('openssl')) {
            $bytes = openssl_random_pseudo_bytes($len);
            return bin2hex($bytes);
        }
        return $len > 10 ? md5(uniqid('', true)) : uniqid('');
    }
}