<?php
namespace Barberry\Filter;
use Barberry\PostedFile\Collection;

class FilterComposite implements FilterInterface {

    /**
     * @var FilterInterface[]
     */
    private $filters = array();

    public function __construct(FilterInterface $filter) {
        foreach (func_get_args() as $k => $filter) {
            if ($filter instanceof FilterInterface) {
                $this->filters[] = $filter;
            }
        }
    }

    /**
     * @param Collection $files
     * @param array $vars
     * @return \Barberry\PostedFile
     */
    public function filter(Collection $files, array $vars) {
        foreach ($this->filters as $filter) {
            $filter->filter($files, $vars);
        }
    }

}