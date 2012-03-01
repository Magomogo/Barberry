<?php
# convert errors into exceptions
set_error_handler(
    create_function(
        '$a, $b, $c, $d',
        'if (0 == error_reporting()) return; throw new ErrorException($b, 0, $a, $c, $d);'
    ),
    E_ALL
);

#autoloader
spl_autoload_register(function($className) {
    $filename = __DIR__ . '/library/' . str_replace('_', '/', $className) . '.php';

    if (file_exists($filename)) {
        include_once($filename);
    } else {
        trigger_error("Can't autoload: " . $className, E_USER_ERROR);
    }
});
