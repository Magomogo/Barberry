<?php
/**
 * @author lion
 */

namespace Barberry\nonlinear {

    function generateDestination($hash, $depth = 3, $len = 2)
    {
        $start = 0;
        $d = $depth;
        $dir = array();

        while ($d-- > 0) {
            $dir[] = substr($hash, $start, $len);
            $start += $len;
        }
        return \Barberry\fs\als(implode(DIRECTORY_SEPARATOR, $dir));
    }

}

namespace Barberry\fs {

    function als($path)
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
