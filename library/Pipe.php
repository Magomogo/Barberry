<?php

class Pipe {
    private $command;

    public function __construct($command) {
        $this->command = $command;
    }

//--------------------------------------------------------------------------------------------------

    /**
     * @throws Pipe_Exception
     * @param null|string $binaryString to put into STDIN
     * @return string read from STDOUT
     */
    public function process($binaryString = null) {
        $pipes = null;
        $proc = proc_open(
            $this->command,
            array(
               0 => array("pipe", 'r'),  // stdin
               1 => array("pipe", "w"),  // stdout
               2 => array("pipe", "w") // stderr
            ),
            $pipes,
            null,
            null,
            array('binary_pipes' => true)
        );

        if (is_resource($proc)) {
            if (!is_null($binaryString)) {
                fwrite($pipes[0], $binaryString);
            }
            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            proc_close($proc);

            if ($error && !strlen($output)) {
                throw new Pipe_Exception($error);
            }

            return $output;

        }
        else {
            throw new Pipe_Exception('Cannot proc_open(' . $this->command . ')');
        }
    }
}
