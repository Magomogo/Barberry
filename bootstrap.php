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

#autoloader
spl_autoload_register(function($className) {
    if (preg_match('/Direction$/', $className)) {
        $filename = Config::get()->directoryEnabledDirection . substr($className, 0, -9) . '.php';
    } else {
        $filename = __DIR__ . '/library/' . str_replace('_', '/', $className) . '.php';
    }

    if (file_exists($filename)) {
        include_once($filename);
    }
});
