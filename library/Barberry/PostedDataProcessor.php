<?php
namespace Barberry;

class PostedDataProcessor {

    private $filterFactory;

    public function __construct($filterFactory) {
        $this->filterFactory = $filterFactory;
    }

    public function process(array $phpFiles, array $request = array()) {
        return $this->filteredFile($request, $this->goodUploadedFiles($phpFiles));
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

    /**
     * @param array $request
     * @param PostedFile[] $allFiles
     * @return PostedFile|null
     */
    private function filteredFile(array $request, array $allFiles) {
        $postedFile = reset($allFiles);

        if ($postedFile === false) {
            return null;
        }

        if (!empty($request)) {
            $filterFactoryMethod = self::filterFactoryMethod($postedFile->bin);
            if (method_exists($this->filterFactory, $filterFactoryMethod)) {
                return $this->filterFactory->$filterFactoryMethod()->filter($request, $allFiles);
            }
        }

        return $postedFile;
    }

    private static function filterFactoryMethod($file) {
        return ContentType::byString($file)->standartExtention() . 'Filter';
    }

}
