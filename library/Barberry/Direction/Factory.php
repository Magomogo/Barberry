<?php
namespace Barberry\Direction;
use Barberry\ContentType;
use Barberry\Plugin;

class Factory {

    /**
     * @param ContentType $sourceContentType
     * @param ContentType $destinationContentType
     * @param null|string $commandPart
     * @throws \Barberry\Plugin\NotAvailableException
     * @return \Barberry\Plugin\InterfaceConverter
     */
    public function direction(ContentType $sourceContentType, ContentType $destinationContentType, $commandPart = null) {

        $directionClassName =
            'Barberry\\Direction\\'
                . 'Direction'
                . ucfirst($sourceContentType->standardExtension())
                . 'To'
                . ucfirst($destinationContentType->standardExtension());

        if (($destinationContentType == $sourceContentType) && !$commandPart) {
            return new Plugin\NullPlugin;
        }

        if(class_exists($directionClassName, true)) {
            return new $directionClassName($commandPart);
        }
        throw new Plugin\NotAvailableException($sourceContentType . ' to '. $destinationContentType);
    }
}
