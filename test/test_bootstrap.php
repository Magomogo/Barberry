<?php
include __DIR__ . '/../bootstrap.php';

require_once 'Mockery/Loader.php';
require_once 'Hamcrest/Hamcrest.php';
$loader = new \Mockery\Loader;
$loader->register();
