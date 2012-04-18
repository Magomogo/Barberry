<?php

class Plugin_OpenOffice_Converter implements Plugin_Interface_Converter {

    /**
     * @var string
     */
    private $tempPath;

    /**
     * @var ContentType
     */
    private $targetContentType;

    public function __construct(ContentType $targetContentType, $tempPath) {
        $this->targetContentType = $targetContentType;
        $this->tempPath = $tempPath;
    }

    public function convert($bin) {
        $source = tempnam($this->tempPath, "ooconverter_");
        chmod($source, 0664);
        $destination = $source . '.' . $this->targetContentType->standartExtention();
        file_put_contents($source, $bin);
        exec(
            'python ' . APPLICATION_PATH . '/externals/pyodconverter/DocumentConverter.py '
                . "$source $destination"
        );
        if (is_file($destination)) {
            $bin = file_get_contents($destination);
            unlink($destination);
        }
        unlink($source);
        return $bin;
    }
}
