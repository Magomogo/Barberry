<?php
namespace Barberry\Filter;

interface FilterInterface {

    /**
     * @param array $vars
     * @param array $allFiles
     * @return \Barberry\PostedFile|null
     */
    public function filter(array $vars, array $allFiles = array());

}
