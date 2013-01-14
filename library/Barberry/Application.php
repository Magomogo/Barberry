<?php
namespace Barberry;
use Barberry\Direction;

class Application
{
    /**
     * @var Resources
     */
    private $resources;

    /**
     * @var RequestSource
     */
    private $requestSource;

    public function __construct(Config $config, Filter\FilterInterface $filter = null, RequestSource $requestSource = null)
    {
        if (is_null($requestSource)) {
            $requestSource = new RequestSource();
        }
        $this->requestSource = $requestSource;

        $this->resources = new Resources($config, $requestSource, $filter);
    }

    /**
     * @return Response
     */
    public function run()
    {
        $controller = new Controller($this->resources->request(), $this->resources->storage(), new Direction\Factory());

        try {
            $response = $controller->{$this->requestSource->_SERVER['REQUEST_METHOD']}();
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
        if('GET' == strtoupper($this->requestSource->_SERVER['REQUEST_METHOD'])) {
            $this->resources->cache()->save($response->body, $this->resources->request());
        } elseif('DELETE' == strtoupper($this->requestSource->_SERVER['REQUEST_METHOD'])) {
            $this->resources->cache()->invalidate($this->resources->request());
        }
    }
}
