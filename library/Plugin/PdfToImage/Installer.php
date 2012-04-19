<?php

class Plugin_PdfToImage_Installer implements Plugin_Interface_Installer {
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
new Plugin_PdfToImage_Converter ('{$this->tempDirectory}');
PHP
        );
    }

}
