<?php
class Plugin_WkHtmlToPdf_Command implements Plugin_Interface_Command {

    /**
     * @param string $commandString
     *
     * @return Plugin_Interface_Command
     */
    public function configure($commandString)
    {
        // TODO: Implement configure() method.
    }

    /**
     * Command should have only one string representation
     *
     * @param string $commandString
     * @return boolean
     */
    public function conforms($commandString)
    {
        return true;
    }
}