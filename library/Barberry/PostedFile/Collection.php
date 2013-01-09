<?php
namespace Barberry\PostedFile;

class Collection implements \ArrayAccess, \Iterator
{

    /**
     * @var \ArrayIterator
     */
    private $specsIterator;

    /**
     * @param array $specs PHP $_FILES specs
     */
    public function __construct(array $specs = array()) {
        $this->specsIterator = new \ArrayIterator(array());

        foreach ($specs as $name => $spec) {
            if (is_array($spec)) {
                if (($spec['error'] == UPLOAD_ERR_OK) && ($spec['size'] > 0)) {
                    $this->specsIterator[$name] = $spec;
                }
            } elseif ($spec instanceof \Barberry\PostedFile) {
                $this->specsIterator[$name] = $spec;
            }
        }
    }

    /**
     * Return the current element
     * @return \Barberry\PostedFile
     */
    public function current() {
        $key = $this->specsIterator->key();

        if ($key === null) {
            return null;
        }

        return $this->getPostedFile($key);
    }

    /**
     * Move forward to next element
     * @return void
     */
    public function next() {
        $this->specsIterator->next();
    }

    /**
     * Return the key of the current element
     * @return string|null
     */
    public function key() {
        return $this->specsIterator->key();
    }

    /**
     * Checks if current position is valid
     * @return boolean
     */
    public function valid() {
        return $this->specsIterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @return void
     */
    public function rewind() {
        $this->specsIterator->rewind();
    }

    /**
     * Whether a offset exists
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        return $this->specsIterator->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @param string $offset
     * @return \Barberry\PostedFile
     */
    public function offsetGet($offset) {
        return $this->getPostedFile($offset);
    }

    /**
     * Offset to set
     * @param string $offset
     * @param \Barberry\PostedFile $value
     * @return void
     * @throws CollectionException
     */
    public function offsetSet($offset, $value) {
        if (!($value instanceof \Barberry\PostedFile)) {
            throw new CollectionException('Wrong type, should be PostedFile');
        }

        $this->specsIterator[$offset] = $value;
    }

    /**
     * Offset to unset
     * @param string $offset
     * @return void
     * @throws CollectionException
     */
    public function offsetUnset($offset) {
        $this->specsIterator->offsetUnset($offset);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function unshift($offset, \Barberry\PostedFile $value) {
        $currentKey = $this->key();
        $this->specsIterator = new \ArrayIterator(
            array_merge(
                array($offset => $value),
                array_diff_key($this->specsIterator->getArrayCopy(), array($offset => false))
            )
        );

        while ($this->specsIterator->key() !== $currentKey) {
            $this->specsIterator->next();
        }
    }

    protected function readTempFile($filepath, $trusted) {
        if (is_uploaded_file($filepath) || $trusted === true) {
            return file_get_contents($filepath);
        }
        return null;
    }

    private function getPostedFile($key) {
        if (!$this->specsIterator->offsetExists($key)) {
            return null;
        }

        $spec = $this->specsIterator[$key];
        if (is_array($spec)) {
            $trusted = (array_key_exists('trusted', $spec) && $spec['trusted'] === true)?:false;
            $this->specsIterator[$key] = new \Barberry\PostedFile(
                $this->readTempFile($spec['tmp_name'], $trusted),
                $spec['name']
            );
        }

        return $this->specsIterator[$key];
    }

}
