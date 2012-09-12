#!/usr/bin/php
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

reportUnixCommand('soffice', 'Please install openoffice.org-headless');
reportUnixCommand('python', 'Please install python');
reportUnixCommand('pdftops', 'Please install poppler (http://poppler.freedesktop.org)');
reportUnixCommand('pdftotext', 'Please install poppler (http://poppler.freedesktop.org)');
reportUnixCommand('convert', 'Please install imagemagic (http://www.imagemagick.org)');
checkOpenOfficeService();

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
        exit(1);
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

//--------------------------------------------------------------------------------------------------

function reportUnixCommand($command, $messageIfMissing) {
    echo "$command command - ";

    if (preg_match('/^\/\w+/', exec("which $command"))) {
        echo "FOUND\n";
    }
    else {
        echo "MISSING - $messageIfMissing\n\n";
        exit;
    }
}

//--------------------------------------------------------------------------------------------------

function checkOpenOfficeService() {
    echo "Open office service - ";

    $fp = @fsockopen('127.0.0.1', '2002');
    if ($fp) {
        echo "LISTENING\n";
    } else {
        echo "MISSING - Run soffice --accept=\"socket,port=2002;urp;\" --headless\n\n";
        exit;
    }
    fclose($fp);
}
