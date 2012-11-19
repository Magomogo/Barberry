<?php
namespace Barberry\Parser;
use Barberry\ContentType;

include_once dirname(dirname(dirname(__DIR__))) . '/externals/Tbs/tbs_class.php';
include_once dirname(dirname(dirname(__DIR__))) . '/externals/Tbs/tbs_plugin_opentbs.php';

class OpenOfficeTemplate implements ParserInterface {

    /**
     * @var \clsTinyButStrong
     */
    private $tbs;

    /**
     * @var string
     */
    private $tempPath;

    public function __construct(\clsTinyButStrong $tbs, $tempPath) {
        $this->tbs = $tbs;
        $this->tempPath = $tempPath;
    }

    /**
     * @param string $template to parse
     * @param array $vars
     * @return string document
     */
    public function parse($template, array $vars) {
        if (strlen($template)) {
            $this->tbs->PlugIn(TBS_INSTALL, OPENTBS_PLUGIN);
            $tempFileName = $this->toTempFileWithExt($template);
            $this->tbs->LoadTemplate($tempFileName, OPENTBS_ALREADY_UTF8);

            foreach ($vars as $key => $val) {
                if (is_array($val)) {
                    $this->tbs->MergeBlock($key, $val);
                } else {
                    $this->tbs->MergeField($key, $val);
                }
            }

            $this->tbs->Show(OPENTBS_STRING);
            unlink($tempFileName);
            return $this->tbs->Source;
        }
        return '';
    }

//--------------------------------------------------------------------------------------------------

    private function toTempFileWithExt($template) {
        $filename = tempnam($this->tempPath, 'ooparser_');
        file_put_contents($filename, $template);
        $ext = ContentType::byString($template)->standardExtension();
        rename($filename, "$filename.$ext");
        return "$filename.$ext";
    }
}
