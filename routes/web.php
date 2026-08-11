<?php
use App\Controllers\HomeController;
use App\Controllers\CompanyController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\InventoryController;
use App\Controllers\ShopController;

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

//administración completa de categorías 
$router->get('/categorias/editar', [
    CategoryController::class,
    'edit'
]);

$router->post('/categorias/actualizar', [
    CategoryController::class,
    'update'
]);

$router->post('/categorias/eliminar', [
    CategoryController::class,
    'destroy'
]);

//registro y listado de productos
$router->get('/productos', [
    ProductController::class,
    'index'
]);

$router->get('/productos/crear', [
    ProductController::class,
    'create'
]);

$router->post('/productos', [
    ProductController::class,
    'store'
]);

//editar y eliminar productos
$router->get('/productos/editar', [
    ProductController::class,
    'edit'
]);

$router->post('/productos/actualizar', [
    ProductController::class,
    'update'
]);

$router->post('/productos/eliminar', [
    ProductController::class,
    'destroy'
]);

//control de inventario
$router->get('/inventario', [
    InventoryController::class,
    'show'
]);

$router->post('/inventario/movimiento', [
    InventoryController::class,
    'store'
]);

//imagenes de productos
$router->post('/productos/imagen', [
    ProductController::class,
    'uploadImage'
]);

$router->post('/productos/imagen/principal', [
    ProductController::class,
    'setPrimaryImage'
]);

$router->post('/productos/imagen/eliminar', [
    ProductController::class,
    'deleteImage'
]);

//catálogo público
$router->get('/tienda', [
    ShopController::class,
    'index'
]);

$router->get('/producto', [
    ShopController::class,
    'show'
]);