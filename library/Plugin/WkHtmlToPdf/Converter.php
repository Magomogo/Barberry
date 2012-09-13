<?php
class Plugin_WkHtmlToPdf_Converter implements Plugin_Interface_Converter {

    /**
     * @var string
     */
    private $tempPath;

    /**
     * @var ContentType
     */
    private $targetContentType;

    public function __construct(ContentType $targetContentType, $tempPath) {
        $this->tempPath = $tempPath;
        $this->targetContentType = $targetContentType;
    }

    public function convert($bin, Plugin_Interface_Command $command = null)
    {
        $url = $bin;
        $filename = $this->tempPath . preg_replace('/[^A-Za-z0-9_\.-]/', '', $url) . '.jpg';
        system($aaa = 'wkhtmltoimage ' . escapeshellarg($bin) . ' ' . escapeshellarg($filename));
        return file_get_contents($filename);
    }
}