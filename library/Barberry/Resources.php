<?php
namespace Barberry;

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

    public function __construct(Config $config)
    {
        $this->config = $config;
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
        return $this->getResource(
            __FUNCTION__,
            function () {
                $dp = new PostedDataProcessor(new Parser\Factory);
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
