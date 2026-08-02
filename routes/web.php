<?php

use App\Controllers\HomeController;
use App\Controllers\CompanyController;

$router->get('/', [
    HomeController::class,
    'index'
]);

$router->get('/empresa', [
    CompanyController::class,
    'index'
]);

$router->get('/empresa/crear', [
    CompanyController::class,
    'create'
]);

$router->post('/empresa', [
    CompanyController::class,
    'store'
]);