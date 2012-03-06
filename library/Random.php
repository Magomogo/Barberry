<?php

class Random {
    public static function generate($len = 10, $chars = 'abcdefghijklmnopqrstuvwxyz0123456789') {
        $num_chars = strlen($chars)-1;
        $result = '';
        for($i = 0; $i < $len; ++$i) {
            $result .= $chars[mt_rand(0, $num_chars)];
        }
        return $result;
    }
}