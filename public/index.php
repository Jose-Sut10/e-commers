<?php

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

use Core\Kernel;

$kernel = new Kernel();

$kernel->handle();