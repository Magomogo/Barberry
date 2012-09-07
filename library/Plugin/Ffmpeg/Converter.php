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
        file_put_contents($source, $bin, FILE_APPEND);
        if ($this->targetContentType->isVideo()) {
            $resize_params = ($command->width() && $command->height()) ? ' -s '.escapeshellarg($command->width().'x'.$command->height()).' ' : '';
            $video_params = ($command->videoBitrate()) ? ' -b '.escapeshellarg($command->videoBitrate().'k').' ': '';
            if (!$video_params)
            {
                // detect original
                $cmd = 'ffmpeg -i '.escapeshellarg($source).' 2>&1 | grep bitrate';
                $output = shell_exec($cmd);
                file_put_contents(__FILE__.'.log', $output.PHP_EOL, FILE_APPEND);
                if (preg_match('|bitrate: (\d+)|', $output, $matches)) {
                    $video_params = ' -b '.escapeshellarg(intval($matches[1]).'k').' ';
                } else {
                    $video_params = ' -vcodec copy ';
                }
            }
            $audio_params = ($command->audioBitrate()) ? ' -ab '.escapeshellarg($command->audioBitrate().'k').' ': '';
            if (!$audio_params) {
                $audio_params = ' -acodec copy ';
            }

            $error = 0;
            ob_start();
            $cmd = 'nice -n 0 ffmpeg -i '.escapeshellarg($source).
                ' '.$resize_params.$video_params.$audio_params.' -sn -strict experimental '.escapeshellarg($destination).' 2>&1';
            passthru($cmd, $error);
            file_put_contents(__FILE__.'.log', $cmd.PHP_EOL, FILE_APPEND);
            $msg = ob_get_clean();
        } elseif ($this->targetContentType->isImage()) {
            $resize_params = ($command->width() && $command->height()) ? ' -s '.escapeshellarg($command->width().'x'.$command->height()).' ' : '';
            $error = 0;
            ob_start();
            $screen_params = ($command->screenshotTime() !== null ? $command->screenshotTime() : 0);
            $cmd = 'nice -n 0 ffmpeg -i '.escapeshellarg($source).
                ' '.$resize_params.' -sn -an -ss '.escapeshellarg($screen_params).' -r 1 -vframes 1 -f mjpeg '.escapeshellarg($destination).' 2>&1';
            passthru($cmd, $error);
            $msg = ob_get_clean();
        } else {
            file_put_contents(__FILE__.'.log',var_export($this->targetContentType,1).PHP_EOL.PHP_EOL, FILE_APPEND);
        }
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
