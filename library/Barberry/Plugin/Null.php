<?php
namespace Barberry\Plugin;

class Null implements InterfaceConverter {

    public function convert($bin, InterfaceCommand $command = null) {
        return $bin;
    }
}
