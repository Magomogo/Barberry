<?php

include __DIR__ . '/../bootstrap.php';

$controller = new Controller(
    new Request($_SERVER['REQUEST_URI'], postedDataProcessor()->process($_FILES, $_POST)),
    new Storage_File(Config::get()->directoryStorage)
);

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

function postedDataProcessor() {
    return new PostedDataProcessor(new Parser_Factory);
}

function invokeCache(Response $response) {
    $cache = new Cache(Config::get()->directoryCache);
    if('GET' == strtoupper($_SERVER['REQUEST_METHOD'])) {
        $cache->save($response->body, new Request($_SERVER['REQUEST_URI']));
    } elseif('DELETE' == strtoupper($_SERVER['REQUEST_METHOD'])) {
        $cache->invalidate(new Request($_SERVER['REQUEST_URI']));
    }
}