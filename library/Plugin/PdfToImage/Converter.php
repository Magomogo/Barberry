<?php

class Plugin_PdfToImage_Converter implements Plugin_Interface_Converter {

    private $tempPath;

    public function __construct($tempPath) {
        $this->tempPath = $tempPath;
    }

    public function convert($bin, $commandString = null) {
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

//--------------------------------------------------------------------------------------------------

    private static function command($commandString) {
        return new Plugin_PdfToImage_Command($commandString);
    }

    private static function pdfToPsPopplerCommand($tmpFilename) {
        return 'pdftops -level3 -f 1 -l 1 -expand ' . escapeshellarg($tmpFilename) . ' -';
    }

    private static function psToJpegImagemagic($width) {
        return "convert -density 150 -[0] -background white -flatten -depth 8 -resize $width jpeg:-";
    }
}
