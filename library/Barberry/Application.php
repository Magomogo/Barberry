<?php

namespace Barberry;

use Symfony\Component\HttpFoundation;
use Barberry\Direction;

class Application
{
    /**
     * @var Resources
     */
    private $resources;

    public function __construct(Config $config, Filter\FilterInterface $filter = null)
    {
        $this->resources = new Resources($config, $filter);
    }

    /**
     * @return HttpFoundation\Response
     */
    public function run(): HttpFoundation\Response
    {
        $controller = new Controller(
            $this->resources->request(),
            $this->resources->storage(),
            $this->resources->cache(),
            new Direction\Factory()
        );

        try {
            return $controller->{strtolower($_SERVER['REQUEST_METHOD'])}();
        } catch (Controller\NotFoundException $e) {

            return self::jsonResponse(null, HttpFoundation\Response::HTTP_NOT_FOUND);
        } catch (Controller\NullPostException $e) {

            return self::jsonResponse(null, HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Controller\NotImplementedException $e) {

            return self::jsonResponse(['msg' => $e->getMessage()], HttpFoundation\Response::HTTP_NOT_IMPLEMENTED);
        } catch (\Exception $e) {
            error_log((string) $e);

            return self::jsonResponse(null, HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return Resources
     */
    public function resources(): Resources
    {
        return $this->resources;
    }

    /**
     * @param mixed $data
     * @param int $status
     * @return HttpFoundation\Response
     */
    private static function jsonResponse($data, $status = 200): HttpFoundation\Response
    {
        return (new HttpFoundation\JsonResponse($data, $status))->setProtocolVersion('1.1');
    }
}
