<?php

include __DIR__ . '/../bootstrap.php';

$r = new Dispatcher(
    new Controller(new Storage_File(Config::get()->directoryStorage)),
    new PostedDataProcessor(new Parser_Factory)
);

$controller = $r->dispatchRequest($_SERVER['REQUEST_URI'], $_FILES, $_POST);

try {
    $response = $controller->{$_SERVER['REQUEST_METHOD']}();

    if('GET' == strtoupper($_SERVER['REQUEST_METHOD'])) {
        $cache = new Cache(Config::get()->directoryCache);
        $cache->save($response->body, $_SERVER['REQUEST_URI']);
    }

} catch (Controller_NotFoundException $e) {
    $response = Response::notFound();
} catch (Exception $e) {
    $response = Response::serverError();
    error_log(strval($e));
}
$response->send();