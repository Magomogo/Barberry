<?php

interface Parser_Interface {
    /**
     * @param string $template to parse
     * @param array $vars
     * @return string document
     */
    public function parse($template, array $vars);
}
