<?php

interface Plugin_Interface_Command {
    /**
     * @param string $commandString
     * @return Plugin_Interface_Command
     */
    public function configure($commandString);

    /**
     * Command should have only one string representation
     *
     * @param string $commandString
     * @return boolean
     */
    public function conforms($commandString);
}
