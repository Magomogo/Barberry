<?php

include __DIR__ . '/../bootstrap.php';

$controller = new Controller(Resources::get()->request(), Resources::get()->storage());

try {
    $response = $controller->{$_SERVER['REQUEST_METHOD']}();
    invokeCache($response);

} catch (Controller_NotFoundException $e) {
    $response = Response::notFound();
} catch (Controller_NotImplementedException $e) {
    $response = Response::notImplemented($e->getMessage());
} catch (Exception $e) {
    $response = Response::serverError();
    error_log(strval($e));
}
$response->send();

//--------------------------------------------------------------------------------------------------

function invokeCache(Response $response) {
    if('GET' == strtoupper($_SERVER['REQUEST_METHOD'])) {
        Resources::get()->cache()->save($response->body, Resources::get()->request());
    } elseif('DELETE' == strtoupper($_SERVER['REQUEST_METHOD'])) {
        Resources::get()->cache()->invalidate(Resources::get()->request());
    }
}