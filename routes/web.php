<?php

use App\Controllers\HomeController;
use App\Controllers\EmpresaController;
use App\Controllers\CompanyController;  

$router->get('/', [
    HomeController::class,
    'index'
]);

$router->get('/empresa', [
    EmpresaController::class,
    'index'
]);

$router->get('/company', [
    CompanyController::class,
    'index'
]);

$router->get('/company/create', [
    CompanyController::class,
    'create'
]);

$router->post('/company', [
    CompanyController::class,
    'store'
]);