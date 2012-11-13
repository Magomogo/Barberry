<?php
namespace Barberry\Filter;

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
     * @param array $vars
     * @param array $allFiles
     * @return \Barberry\PostedFile
     */
    public function filter(array $vars, array $allFiles = array()) {
        foreach ($this->filters as $filter) {
            $return = $filter->filter($vars, $allFiles);
            if (!is_null($return)) {
                return $return;
            }
        }

        return null;
    }

}