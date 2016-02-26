<?php
/**
 * @author lion
 */

namespace Barberry\file {

    function als($path)
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}