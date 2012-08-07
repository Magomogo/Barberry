<?php

class Plugin_Ffmpeg_Converter implements Plugin_Interface_Converter {

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

    public function convert($bin, Plugin_Interface_Command $command = null) {
        $source = tempnam($this->tempPath, "ffmpeg_");
        chmod($source, 0664);
        $destination = $source.'.'.$this->targetContentType->standartExtention();
        //file_put_contents($source, $bin, FILE_APPEND);

        $resize = ($command->width() && $command->height()) ? ' -s '.escapeshellarg($command->width().'x'.$command->height()).' ' : '';
        $video_bitrate = ($command->videoBitrate()) ? ' -b '.escapeshellarg($command->videoBitrate().'k').' ': '';
        if (!$video_bitrate)
        {
            // detect original
            $cmd = 'ffmpeg -i '.escapeshellarg($source).' 2>&1 | grep bitrate';
            $output = shell_exec($cmd);
            preg_match('|bitrate: (\d+)|', $output, $matches);
            $video_bitrate = ' -b '.escapeshellarg(intval($matches[1]).'k').' ';
        }
        $audio_bitrate = ($command->audioBitrate()) ? ' -ab '.escapeshellarg($command->audioBitrate().'k').' ': '';
        if (!$audio_bitrate)
        {
            // detect original
            $cmd = 'ffmpeg -i '.escapeshellarg($source).' 2>&1 | grep "Audio:"';
            $output = shell_exec($cmd);
            preg_match('|, (\d+) Hz|', $output, $matches);
            preg_match('|, (\d+) kb/s|', $output, $matches2);
            // ' -ar '.escapeshellarg(intval($matches[1])).
            $audio_bitrate = ' -ab '.escapeshellarg(intval($matches2[1]).'k').' ';
        }

        $error = 0;
        ob_start();
        // ' -f '.$this->targetContentType->standartExtention().
        $cmd = 'nice -n 0 ffmpeg -i '.escapeshellarg($source).
            ' '.$resize.$video_bitrate.$audio_bitrate.' -sn -strict experimental '.escapeshellarg($destination).' 2>&1';
        //file_put_contents(__FILE__.'.log', $cmd.PHP_EOL, FILE_APPEND);
        passthru($cmd, $error);
        $msg = ob_get_clean();
        //file_put_contents(__FILE__.'.log',$msg.PHP_EOL.var_export($error,1).PHP_EOL.PHP_EOL, FILE_APPEND);
        ob_end_clean();
        if (is_file($destination) && !$error) {
            $bin = file_get_contents($destination);
            unlink($destination);
        }
        else
        {
            throw new Cache_Exception($msg);
        }
        unlink($source);
        return $bin;
    }
}
