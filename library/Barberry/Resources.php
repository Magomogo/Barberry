<?php
namespace Barberry;
use Barberry\Filter;

class Resources
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $initializedResources = array();

    /**
     * @var Filter\FilterInterface
     */
    private $filter;

    /**
     * @param Config $config
     * @param Filter\FilterInterface $filter
     */
    public function __construct(Config $config, Filter\FilterInterface $filter = null)
    {
        $this->config = $config;
        $this->filter = $filter;
    }

    /**
     * @return Cache
     */
    public function cache()
    {
        $config = $this->config;
        return $this->getResource(
            __FUNCTION__,
            function () use ($config) {
                return new Cache($config->directoryCache);
            }
        );
    }

    /**
     * @return Storage\StorageInterface
     */
    public function storage()
    {
        $config = $this->config;
        return $this->getResource(
            __FUNCTION__,
            function () use ($config) {
                return new Storage\File($config->directoryStorage);
            }
        );
    }

    /**
     * @return Request
     */
    public function request()
    {
        $filter = $this->filter;
        return $this->getResource(
            __FUNCTION__,
            function () use ($filter) {
                $dp = new PostedDataProcessor($filter);
                return new Request(
                    array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : '/',
                    $dp->process($_FILES, $_POST)
                );
            }
        );
    }

//--------------------------------------------------------------------------------------------------

    private function getResource($name, $initCallback)
    {
        if (!array_key_exists($name, $this->initializedResources)) {
            $this->initializedResources[$name] = $initCallback();
        }
        return $this->initializedResources[$name];
    }
}
