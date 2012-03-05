<?php

include __DIR__ . '/../bootstrap.php';

$r = new Dispatcher(new Controller(new Storage_File()), new FileLoader);

$controller = $r->dispatchRequest($_SERVER['REQUEST_URI'], $_FILES);
$response = $controller->{$_SERVER['REQUEST_METHOD']}();
$response->send();