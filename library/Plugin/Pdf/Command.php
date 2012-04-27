<?php

class Plugin_Pdf_Command implements Plugin_Interface_Command {

    private $width;

    /**
     * @param string $commandString
     * @return Plugin_Pdf_Command
     */
    public function configure($commandString) {
        $width = is_numeric($commandString) ? intval($commandString) : 800;
        $this->width = min(2000, max(10, $width));
        return $this;
    }

    public function width() {
        return $this->width;
    }

    public function __toString() {
        return strval($this->width);
    }

    /**
     * Command should have only one string representation
     *
     * @param string $commandString
     * @return boolean
     */
    public function conforms($commandString) {
        return strval($this) === $commandString;
    }
}
