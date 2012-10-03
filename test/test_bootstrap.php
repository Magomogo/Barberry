<?php
include __DIR__ . '/../vendor/autoload.php';

require_once 'Mockery/Loader.php';
require_once 'Hamcrest/Hamcrest.php';
$loader = new \Mockery\Loader;
$loader->register();
