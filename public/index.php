<?php

include __DIR__ . '/../bootstrap.php';

$r = new Dispatcher(
    new Controller(new Storage_File()),
    new PostedDataProcessor(new Parser_Factory)
);

$controller = $r->dispatchRequest($_SERVER['REQUEST_URI'], $_FILES, $_POST);

try {
    $response = $controller->{$_SERVER['REQUEST_METHOD']}();
} catch (Controller_NotFoundException $e) {
    $response = Response::notFound();
} catch (Exception $e) {
    $response = Response::serverError();
    error_log(strval($e));
}
$response->send();