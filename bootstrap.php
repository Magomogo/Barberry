<?php
error_reporting(E_ALL | E_STRICT);
define('APPLICATION_PATH', __DIR__);

# convert errors into exceptions
set_error_handler(
    create_function(
        '$a, $b, $c, $d',
        'if (0 == error_reporting()) return; throw new ErrorException($b, 0, $a, $c, $d);'
    ),
    E_ALL
);

include __DIR__ . '/vendor/autoload.php';