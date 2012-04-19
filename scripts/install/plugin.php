#!/usr/bin/php
<?php

include __DIR__ . '/../../bootstrap.php';

if ($argc < 2) {
    die(helpMessage());
}

array_shift($argv);

foreach (array_unique($argv) as $pluginName) {
    $functionName = strtolower($pluginName) . 'Installer';
    if (function_exists($functionName)) {
        echo "\nINSTALLING: $pluginName...";

        try {
            $functionName()->install(directionComposer());
            echo "DONE";
        } catch (Exception $e) {
            echo "ERROR: " . $e . "\n\n";
        }

    } else {
        echo "\nWARNING: plugin $pluginName is not available\n";
    }
}

echo "\nFINISHED\n";

//--------------------------------------------------------------------------------------------------

function openofficeInstaller() {
    return new Plugin_OpenOffice_Installer(Config::get()->directoryTemp);
}

function pdftoimageInstaller() {
    return new Plugin_PdfToImage_Installer(Config::get()->directoryTemp);
}

function directionComposer() {
    return new Direction_Composer(Config::get()->directoryEnabledDirection);
}

function helpMessage() {
    return <<<TEXT

This scripts deploys named plugins
Usage: ./plugin.php name1[ name2... nameN]


TEXT;
}