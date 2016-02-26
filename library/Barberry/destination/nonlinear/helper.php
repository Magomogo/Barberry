<?php
/**
 * @author lion
 */

namespace Barberry\destination\nonlinear {

    use function Barberry\file\als;

    function generate($hash, $depth = 3, $len = 2)
    {
        $start = 0;
        $d = $depth;
        $dir = array();

        while ($d-- > 0) {
            $dir[] = substr($hash, $start, $len);
            $start += $len;
        }
        return als(implode(DIRECTORY_SEPARATOR, $dir));
    }

}