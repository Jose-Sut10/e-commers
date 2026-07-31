<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Kernel;

$kernel = new Kernel();

$kernel->handle();