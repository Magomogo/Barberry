<?php
namespace Barberry;

class Application
{
    /**
     * @var Resources
     */
    private $resources;

    public function __construct(Config $config)
    {
        $this->resources = new Resources($config);
    }

    /**
     * @return Response
     */
    public function run()
    {
        $controller = new Controller($this->resources->request(), $this->resources->storage());

        try {
            $response = $controller->{$_SERVER['REQUEST_METHOD']}();
            $this->invokeCache($response);

            return $response;
        } catch (Controller\NotFoundException $e) {

            return Response::notFound();
        } catch (Controller\NotImplementedException $e) {

            return  Response::notImplemented($e->getMessage());
        } catch (\Exception $e) {
            error_log(strval($e));

            return  Response::serverError();
        }
    }

    private function invokeCache(Response $response)
    {
        if('GET' == strtoupper($_SERVER['REQUEST_METHOD'])) {
            $this->resources->cache()->save($response->body, $this->resources->request());
        } elseif('DELETE' == strtoupper($_SERVER['REQUEST_METHOD'])) {
            $this->resources->cache()->invalidate($this->resources->request());
        }
    }
}
