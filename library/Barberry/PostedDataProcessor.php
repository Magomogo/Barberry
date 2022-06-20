<?php

namespace Barberry;

use Barberry\Filter;

class PostedDataProcessor
{
    /**
     * @var Filter\FilterInterface
     */
    private $filter;

    /**
     * @param Filter\FilterInterface $filter
     */
    public function __construct(Filter\FilterInterface $filter = null)
    {
        $this->filter = $filter;
    }

    /**
     * @param array $phpFiles
     * @param array $request
     * @return PostedFile|null
     */
    public function process(array $phpFiles, array $request = array())
    {
        $filesCollection = $this->createCollection($phpFiles);

        if (!is_null($this->filter)) {
            $this->filter->filter($filesCollection, $request);
        }

        $filesCollection->rewind();
        return $filesCollection->current();
    }

    protected function createCollection($phpFiles)
    {
        return new \Barberry\PostedFile\Collection($phpFiles);
    }
}
