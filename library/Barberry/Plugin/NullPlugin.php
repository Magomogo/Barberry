<?php
namespace Barberry\Plugin;
use Barberry\ContentType;

class NullPlugin implements InterfaceConverter {

    public function convert($bin, InterfaceCommand $command = null) {
        return $bin;
    }

    /**
     * @param ContentType $targetContentType
     * @param string $tempPath
     * @return self
     */
    public function configure(ContentType $targetContentType, $tempPath)
    {
        return $this;
    }
}
