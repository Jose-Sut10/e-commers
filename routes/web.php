<?php
use App\Controllers\HomeController;
use App\Controllers\CompanyController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\CategoryController;

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

//modulo de usuarios
$router->get('/usuarios', [
    UserController::class,
    'index'
]);

$router->get('/usuarios/crear', [
    UserController::class,
    'create'
]);

$router->post('/usuarios', [
    UserController::class,
    'store'
]);

//editar usuarios
$router->get('/usuarios/editar', [
    UserController::class,
    'edit'
]);

$router->post('/usuarios/actualizar', [
    UserController::class,
    'update'
]);

//eliminar usuarios
$router->post('/usuarios/eliminar', [
    UserController::class,
    'destroy'
]);

//agregando categorías
$router->get('/categorias', [
    CategoryController::class,
    'index'
]);

$router->get('/categorias/crear', [
    CategoryController::class,
    'create'
]);

$router->post('/categorias', [
    CategoryController::class,
    'store'
]);