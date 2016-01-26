<?php
namespace Barberry\Storage;

use Barberry\Storage\File\NonLinearDestination;
use Barberry\Uniq;

class File implements StorageInterface {

    const FILE_MODE = 0664;
    const DIR_MODE = 0775;

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
        $id = $this->generateUniqueId();
        $filePath = $this->filePathById($id);

        $bytes = file_put_contents($filePath, $content);
        if ($bytes === false) {
            $error = error_get_last();
            throw new WriteException($id, $error['message']);
        }

        if (is_file($filePath)) {
            return $id;
        }
        throw new WriteException($id);
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

    private function generateUniqueId() {
        $destination = NonLinearDestination::factory($uId = Uniq::id())->make($this->permanentStoragePath, self::DIR_MODE);
        file_put_contents($emptyFile = $destination . $uId, '');
        chmod($emptyFile, self::FILE_MODE);

        return $uId;
    }

    private function filePathById($id) {
        if (is_file($f = $this->permanentStoragePath . $id)) {
            return $f;
        }

        $d = NonLinearDestination::factory($id)->generate();

        return $this->permanentStoragePath . $d . $id;
    }
}
