<?php
use App\Controllers\HomeController;
use App\Controllers\CompanyController;
use App\Controllers\AuthController;

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

$router->get('/empresa/editar', [
    CompanyController::class,
    'edit'
]);

$router->post('/empresa/actualizar', [
    CompanyController::class,
    'update'
]);

//inicio de sesión
$router->get('/login', [
    AuthController::class,
    'showLogin'
]);

$router->post('/login', [
    AuthController::class,
    'login'
]);

$router->post('/logout', [
    AuthController::class,
    'logout'
]);