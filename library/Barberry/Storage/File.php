<?php
namespace Barberry\Storage;

use Barberry\Storage\File\NonLinearDestination;

class File implements StorageInterface {

    private $permanentStoragePath;

    public function __construct($path) {
        $this->permanentStoragePath = NonLinearDestination::als($path);
    }

    /**
     * @param string $id
     * @return string
     * @throws NotFoundException
     */
    public function getById($id) {
        $filePath = $this->filePathById($id);

        if (is_file($filePath)) {
            return file_get_contents($filePath);
        } else {
            throw new NotFoundException($filePath);
        }
    }

    /**
     * @param string $content
     * @return string content id
     * @throws WriteException
     */
    public function save($content) {
        $id = NonLinearDestination::factory($this->permanentStoragePath)->getBase();
        $filePath = $this->filePathById($id);

        $bytes = file_put_contents($filePath, $content);
        if ($bytes === false) {
            $error = error_get_last();
            throw new WriteException($id, $error['message']);
        }

        return $id;
    }

    /**
     * @param string $id
     * @throws NotFoundException
     */
    public function delete($id) {
        $filePath = $this->filePathById($id);

        if (is_file($filePath)) {
            unlink($filePath);
        } else {
            throw new NotFoundException($filePath);
        }
    }

    /**
     * @param $id
     * @return string
     */
    private function filePathById($id) {
        if (is_file($f = $this->permanentStoragePath . $id)) {
            return $f;
        }

        $d = NonLinearDestination::factory($this->permanentStoragePath, $id)->generate();

        return $d . $id;
    }
}
