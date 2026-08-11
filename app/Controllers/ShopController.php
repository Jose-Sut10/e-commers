<?php
namespace App\Controllers;
use Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\ProductImage;

class ShopController extends Controller{
    public function index(): void{
        $categorySlug = trim(
            (string) ($_GET['categoria'] ?? '')
        );

        view('shop/index', [
            'title' => 'Tienda',

            'company' =>
                Company::first(),

            'categories' =>
                Category::active(),

            'products' =>
                Product::publicCatalog(
                    $categorySlug === ''
                        ? null
                        : $categorySlug
                ),

            'selectedCategory' =>
                $categorySlug,
        ]);
    }

    public function show(): void{
        $slug = trim(
            (string) ($_GET['slug'] ?? '')
        );

        if ($slug === '') {
            http_response_code(404);

            view('errors/404', [
                'title' => 'Producto no encontrado',
            ]);

            return;
        }

        $product =
            Product::findPublicBySlug(
                $slug
            );

        if (!$product) {
            http_response_code(404);

            view('errors/404', [
                'title' => 'Producto no encontrado',
            ]);

            return;
        }

        view('shop/show', [
            'title' => $product->name,

            'company' =>
                Company::first(),

            'product' =>
                $product,

            'images' =>
                ProductImage::forProduct(
                    (int) $product->id
                ),
        ]);
    }
}