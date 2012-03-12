<?php
error_reporting(E_ALL | E_STRICT);

echo "\nChecking pre-requisites required to install, develop, and run the application:\n\n";

reportVersion('PHP', '5.3.0', PHP_VERSION);

$can_proceed = true;
foreach (
    array(
        'fileinfo',
        'curl'
    ) as $requiredPhpExtension) {
    $can_proceed &= reportPhpExtension($requiredPhpExtension);
}

if (!$can_proceed) {
    echo "Please install missed PHP extenstions.\n\n";
    exit;
}

echo "\nAll critical checks passed SUCCESSFULLY\n\n";

//==================================================================================================

function reportVersion($componentName, $requiredVersion, $currentVersion, $whiteList = array()) {
    echo "$componentName version: $currentVersion - ";

    if(!empty($whiteList) && in_array($currentVersion, $whiteList)) {
        echo "OK\n";
    }
    else if (version_compare($requiredVersion, $currentVersion) <= 0) {
        echo "OK\n";
    }
    else {
        echo "INSUFFICIENT - $requiredVersion required\n\n";
        exit;
    }
}

//--------------------------------------------------------------------------------------------------

function reportPhpExtension($name) {
    echo "$name module - ";

    if (extension_loaded($name)) {
        echo "LOADED\n";
        return true;
    }
    else {
         echo "MISSING\n";
         return false;
    }
}