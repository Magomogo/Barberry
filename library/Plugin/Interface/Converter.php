<?php

interface Plugin_Interface_Converter {

    public function convert($bin, Plugin_Interface_Command $command = null);
}
