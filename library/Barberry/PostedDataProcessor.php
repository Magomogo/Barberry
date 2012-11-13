<?php
namespace Barberry;
use Barberry\Filter;

class PostedDataProcessor {

    /**
     * @var Filter\FilterInterface
     */
    private $filter;

    /**
     * @param Filter\FilterInterface $filter
     */
    public function __construct(Filter\FilterInterface $filter = null) {
        $this->filter = $filter;
    }

    /**
     * @param array $phpFiles
     * @param array $request
     * @return PostedFile|null
     */
    public function process(array $phpFiles, array $request = array()) {
        if (!is_null($this->filter)) {
            return $this->filter->filter($request, $this->goodUploadedFiles($phpFiles));
        } else {
            foreach ($phpFiles as $key => $file) {
                $files = $this->goodUploadedFiles(array($key => $file));
                if (!empty($files)) {
                    return reset($files);
                }
            }
        }

        return null;
    }

//--------------------------------------------------------------------------------------------------

    protected function readTempFile($filepath) {
        if (is_uploaded_file($filepath)) {
            return file_get_contents($filepath);
        }
        return null;
    }

    /**
     * @param array $phpFiles
     * @return PostedFile[]
     */
    private function goodUploadedFiles(array $phpFiles) {
        $files = array();

        foreach ($phpFiles as $name => $spec) {
            if (($spec['error'] == UPLOAD_ERR_OK) && ($spec['size'] > 0)) {
                $files[$name] = new PostedFile($this->readTempFile($spec['tmp_name']), $spec['name']);
            }
        }

        return $files;
    }

}
