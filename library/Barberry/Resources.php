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
     * @var RequestSource
     */
    private $requestSource;


    /**
     * @param Config $config
     * @param Filter\FilterInterface $filter
     */
    public function __construct(Config $config, Filter\FilterInterface $filter = null, RequestSource $requestSource)
    {
        $this->config = $config;
        $this->filter = $filter;
        $this->requestSource = $requestSource;
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
                    array_key_exists('REQUEST_URI', $this->requestSource->_SERVER)
                        ? $this->requestSource->_SERVER['REQUEST_URI']
                        : '/',
                    $dp->process($this->requestSource->_FILES, $this->requestSource->_POST)
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
