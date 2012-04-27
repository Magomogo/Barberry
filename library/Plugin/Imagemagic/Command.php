<?php

class Plugin_Imagemagic_Command implements Plugin_Interface_Command {

    /**
     * @param string $commandString
     * @return Plugin_Imagemagic_Command
     */
    public function configure($commandString) {
        return $this;
    }

    public function conforms($commandString) {
        return true;
    }
}
