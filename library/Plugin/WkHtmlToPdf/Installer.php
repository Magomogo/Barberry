<?php
class Plugin_WkHtmlToPdf_Installer implements Plugin_Interface_Installer {
    /**
     * @var string
     */
    private $tempDirectory;

    public function __construct($tempDirectory) {
        $this->tempDirectory = $tempDirectory;
    }

    public function install(Direction_Composer $composer) {
        $composer->writeClassDeclaration(
            ContentType::url(),
            ContentType::jpeg(),
            <<<PHP
new Plugin_WkHtmlToPdf_Converter (ContentType::jpeg(), '{$this->tempDirectory}');
PHP
            ,
            'new Plugin_WkHtmlToPdf_Command'
        );
    }

}