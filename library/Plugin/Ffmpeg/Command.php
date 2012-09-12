<?php

class Plugin_Ffmpeg_Command implements Plugin_Interface_Command {

    const MAX_VIDEO_BITRATE = 4000;
    const MAX_AUDIO_BITRATE = 256;
    const MAX_WIDTH = 1280;
    const MAX_HEIGHT = 720;

    private $_videoBitrate = null;
    private $_audioBitrate = null;
    private $_screenshotTime = null;
    private $_width = null;
    private $_height = null;

    /**
     * @param string $commandString
     * @return Plugin_Imagemagic_Command
     */
    public function configure($commandString) {
        $params = explode("_",$commandString);
        foreach ($params as $val) {
            if (preg_match("@^v([\d]*)$@", $val, $regs)) {
                $this->_videoBitrate = strlen($regs[1]) ? min((int)$regs[1], self::MAX_VIDEO_BITRATE) : null;
            }
            else if (preg_match("@^a([\d]*)$@", $val, $regs)) {
                $this->_audioBitrate = strlen($regs[1]) ? min((int)$regs[1], self::MAX_AUDIO_BITRATE) : null;
            }
            else if (preg_match("@^t([\d]*)$@", $val, $regs)) {
                $this->_screenshotTime = strlen($regs[1]) ? max((int)$regs[1], 0) : null;
            }
            else if (preg_match("@^([\d]*)x([\d]*)$@",$val,$regs)) {
                $this->_width = strlen($regs[1]) ? min((int)$regs[1], self::MAX_WIDTH) : null;
                $this->_height = strlen($regs[2]) ? min((int)$regs[2], self::MAX_HEIGHT) : null;
            }
        }
        return $this;
    }

    public function conforms($commandString) {
        return true;
    }

    public function videoBitrate() {
        return $this->_videoBitrate;
    }

    public function audioBitrate() {
        return $this->_audioBitrate;
    }

    public function screenshotTime() {
        return $this->_screenshotTime;
    }


    public function width() {
        return $this->_width;
    }

    public function height() {
        return $this->_height;
    }

    public function __toString() {
        $opts = array();
        if ($this->_width && $this->_height)
            $opts[] = $this->_width.'x'.$this->_height;
        if ($this->_videoBitrate)
            $opts[] = 'v'.$this->_videoBitrate;
        if ($this->_audioBitrate)
            $opts[] = 'a'.$this->_audioBitrate;
        if ($this->_screenshotTime)
            $opts[] = 't'.$this->_screenshotTime;
        return implode('_', $opts);
    }
}
