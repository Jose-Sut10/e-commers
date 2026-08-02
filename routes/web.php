<?php

use App\Controllers\HomeController;
use App\Controllers\EmpresaController;

$router->get('/', [
    HomeController::class,
    'index'
]);

$router->get('/empresa', [
    EmpresaController::class,
    'index'
]);