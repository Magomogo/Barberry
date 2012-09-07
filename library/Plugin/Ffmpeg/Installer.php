<?php

class Plugin_Ffmpeg_Installer implements Plugin_Interface_Installer {

    /**
     * @var string
     */
    private $tempDirectory;

    public function __construct($tempDirectory) {
        $this->tempDirectory = $tempDirectory;
    }

    public function install(Direction_Composer $composer) {
        foreach (self::directions() as $pair) {
            $composer->writeClassDeclaration(
                $pair[0],
                eval('return ' .$pair[1] . ';'),
                <<<PHP
new Plugin_Ffmpeg_Converter ($pair[1], '{$this->tempDirectory}');
PHP
                ,
                'new Plugin_Ffmpeg_Command'
            );
        }
    }

//--------------------------------------------------------------------------------------------------

    private static function directions() {
        $videos = array('flv', 'webm', 'wmv', 'mpg', 'mpeg', 'avi', 'mkv', 'mp4', 'mov', 'qt', 'ogv', '_3gp', );
        $additional = array('jpg');
        $result = array();
        foreach ($videos as $from)
        {
            foreach ($videos as $to)
            {
                $result[] = array(call_user_func(array('ContentType', $from)), 'ContentType::'.$to.'()');
            }
            foreach ($additional as $to)
            {
                $result[] = array(call_user_func(array('ContentType', $from)), 'ContentType::'.$to.'()');
            }
        }
        return $result;
    }
}
