<?php

class Plugin_OpenOffice_Installer implements Plugin_Interface_Installer {
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
new Plugin_OpenOffice_Converter ($pair[1], '{$this->tempDirectory}');
PHP
            );
        }
    }

//--------------------------------------------------------------------------------------------------

    private static function directions() {
        return array(
            array(ContentType::odt(), 'ContentType::doc()'),
            array(ContentType::ots(), 'ContentType::xls()'),
            array(ContentType::ods(), 'ContentType::xls()'),
            array(ContentType::ott(), 'ContentType::doc()'),
            array(ContentType::ott(), 'ContentType::pdf()'),
        );
    }
}
