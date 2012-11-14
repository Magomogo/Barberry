<?php

namespace Barberry;
use Barberry\ContentType;

/**
 * @property-read string $bin
 * @property-read string $filename
 *
 */
class PostedFile
{

    private $_bin;

    private $_filename;

    private $standardExtension;

    /**
     * @param string $bin
     * @param string $filename
     */
    public function __construct($bin, $filename = null)
    {
        $this->_bin = $bin;
        $this->_filename = $filename;
    }

    public function __get($property)
    {
        if (property_exists($this, '_' . $property)) {
            return $this->{'_' . $property};
        }
        trigger_error('Undefined property via __get(): ' . $property, E_USER_NOTICE);
        return null;
    }

    public function getStandardExtension()
    {
        if (is_null($this->standardExtension) && !is_null($this->_bin)) {
            $this->standardExtension = ContentType::byString($this->_bin)->standartExtention();
        }

        return $this->standardExtension;
    }

}
