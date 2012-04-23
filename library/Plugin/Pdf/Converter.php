<?php

class Plugin_Pdf_Converter implements Plugin_Interface_Converter {

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

    public function convert($bin, $commandString = null) {
        if ($this->targetContentType->standartExtention() == 'txt') {
            return $this->convertToText($bin);
        }
        return $this->convertToJpeg($bin, $commandString);
    }

//--------------------------------------------------------------------------------------------------

    private function convertToText($bin) {
        $pipe = new Pipe('pdftotext - -');
        return $pipe->process($bin);
    }

    private function convertToJpeg($bin, $commandString) {
        $filename = tempnam($this->tempPath, 'pftops_');
        file_put_contents($filename, $bin);

        $pipePdf2Ps = new Pipe(self::pdfToPsPopplerCommand($filename));
        $pipePs2Jpeg = new Pipe(self::psToJpegImagemagic(
            self::command($commandString)->width()
        ));

        $jpeg = $pipePs2Jpeg->process($pipePdf2Ps->process());
        unlink($filename);
        return $jpeg;
    }

    private static function command($commandString) {
        return new Plugin_Pdf_Command($commandString);
    }

    private static function pdfToPsPopplerCommand($tmpFilename) {
        return 'pdftops -level3 -f 1 -l 1 -expand ' . escapeshellarg($tmpFilename) . ' -';
    }

    private static function psToJpegImagemagic($width) {
        return "convert -density 150 -[0] -background white -flatten -depth 8 -resize $width jpeg:-";
    }
}
