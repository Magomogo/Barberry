<?php

include __DIR__ . '/../bootstrap.php';

$controller = new Controller(
    new Request($_SERVER['REQUEST_URI'], postedDataProcessor()->process($_FILES, $_POST)),
    new Storage_File(Config::get()->directoryStorage)
);

try {
    $response = $controller->{$_SERVER['REQUEST_METHOD']}();

    if('GET' == strtoupper($_SERVER['REQUEST_METHOD'])) {
        $cache = new Cache(Config::get()->directoryCache);
        $cache->save($response->body, new Request($_SERVER['REQUEST_URI']));
    }

} catch (Controller_NotFoundException $e) {
    $response = Response::notFound();
} catch (Exception $e) {
    $response = Response::serverError();
    error_log(strval($e));
}
$response->send();

//--------------------------------------------------------------------------------------------------

function postedDataProcessor() {
    return new PostedDataProcessor(new Parser_Factory);
}