<?php

class Plugin_Pdf_Installer implements Plugin_Interface_Installer {
    /**
     * @var string
     */
    private $tempDirectory;

    public function __construct($tempDirectory) {
        $this->tempDirectory = $tempDirectory;
    }

    public function install(Direction_Composer $composer) {
        $composer->writeClassDeclaration(
            ContentType::pdf(),
            ContentType::jpeg(),
            <<<PHP
new Plugin_Pdf_Converter (ContentType::jpeg(), '{$this->tempDirectory}');
PHP
            ,
            'new Plugin_Pdf_Command'
        );
        $composer->writeClassDeclaration(
            ContentType::pdf(),
            ContentType::txt(),
            <<<PHP
new Plugin_Pdf_Converter (ContentType::txt(), '{$this->tempDirectory}');
PHP
        );
    }

}
