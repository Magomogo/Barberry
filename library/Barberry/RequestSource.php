<?php
namespace Barberry;

class RequestSource
{
    /**
     * @var array
     */
    private $_SERVER;

    /**
     * @var array
     */

    private $_POST;

    /**
     * @var array
     */
    private $_FILES;

    public function __construct(array $propertiesToOverride = array())
    {
        $this->setDefaultValues();

        foreach ($propertiesToOverride as $key => $value) {
            if (property_exists($this, $key) && (gettype($value) === gettype($this->$key))) {
                $this->$key = $value;
            }
        }

    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }
        trigger_error('Undefined property via __get(): ' . $property, E_USER_NOTICE);
        return null;
    }

    private function setDefaultValues()
    {
        $this->_SERVER = array_merge(
            array(
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ),
            $_SERVER
        );
        $this->_FILES = $_FILES;
        $this->_POST = $_POST;
    }

}
