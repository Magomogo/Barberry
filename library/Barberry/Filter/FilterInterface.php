<?php
namespace Barberry\Filter;
use Barberry\PostedFile\Collection;

interface FilterInterface {

    /**
     * @param Collection $files
     * @param array $vars
     * @return void
     */
    public function filter(Collection $files, array $vars);

}
