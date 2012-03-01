<?php

include __DIR__ . '/../bootstrap.php';

$r = new Dispatcher();

$controller = $r->dispatch($_SERVER['REQUEST_URI'], $_REQUEST);
$response = $controller->{$_SERVER['REQUEST_METHOD']}();
$response->send();