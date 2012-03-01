<?php

if (!version_compare(PHP_VERSION, '5.3.0', '>=')) {
    die ("PHP 5.3.0 or later required\n");
} else {
    echo "PHP " . PHP_VERSION . ": OK";
}

echo "\nAll critical checks passed SUCCESSFULLY\n\n";