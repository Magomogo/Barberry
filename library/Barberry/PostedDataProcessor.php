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
        $uploadedFiles = $this->goodUploadedFiles($phpFiles);

        if (!is_null($this->filter)) {
            $filtered = $this->filter->filter($request, $uploadedFiles);
            if (!is_null($filtered)) {
                return $filtered;
            }
        }

        if (!empty($uploadedFiles)) {
            return reset($uploadedFiles);
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
