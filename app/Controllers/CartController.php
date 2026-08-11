<?php
namespace App\Controllers;
use Throwable;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Product;
use App\Models\Company;
use App\Services\CartService;

class CartController extends Controller{
    public function index(): void{
        $cart = new CartService();

        view('cart/index', [
            'title' => 'Carrito',

            'company' =>
                Company::first(),

            'items' =>
                $cart->items(),

            'subtotal' =>
                $cart->subtotal(),
        ]);
    }

    public function add(): void{
        $request = new Request();
        $input = $request->all();

        $productId = filter_var(
            $input['product_id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        $quantity = filter_var(
            $input['quantity'] ?? 1,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (
            !$productId
            || !$quantity
        ) {
            Session::flash(
                'warning',
                'Los datos del producto no son válidos.'
            );

            redirect('tienda');
        }

        $product =
            Product::findPublicById(
                $productId
            );

        if (!$product) {
            Session::flash(
                'warning',
                'El producto ya no está disponible.'
            );

            redirect('tienda');
        }

        try {
            $cart =
                new CartService();

            $cart->add(
                $product,
                $quantity
            );

            Session::flash(
                'success',
                'El producto fue agregado al carrito.'
            );

            redirect('carrito');

        } catch (Throwable $exception) {
            Session::flash(
                'warning',
                $exception->getMessage()
            );

            redirect(
                'producto?slug='
                . urlencode(
                    (string) $product->slug
                )
            );
        }
    }

    public function update(): void{
        $request = new Request();
        $input = $request->all();

        $productId = filter_var(
            $input['product_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $quantity = filter_var(
            $input['quantity'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 0,
                ],
            ]
        );

        if (
            !$productId
            || $quantity === false
        ) {
            Session::flash(
                'warning',
                'La cantidad indicada no es válida.'
            );

            redirect('carrito');
        }

        try {
            (
                new CartService()
            )->update(
                (int) $productId,
                (int) $quantity
            );

            Session::flash(
                'success',
                'El carrito fue actualizado.'
            );

        } catch (Throwable $exception) {
            Session::flash(
                'warning',
                $exception->getMessage()
            );
        }

        redirect('carrito');
    }

    public function remove(): void{
        $request = new Request();

        $productId = filter_var(
            $request->input(
                'product_id'
            ),
            FILTER_VALIDATE_INT
        );

        if ($productId) {
            (
                new CartService()
            )->remove(
                (int) $productId
            );

            Session::flash(
                'success',
                'El producto fue eliminado del carrito.'
            );
        }

        redirect('carrito');
    }

    public function clear(): void{
        (
            new CartService()
        )->clear();

        Session::flash(
            'success',
            'El carrito fue vaciado.'
        );

        redirect('carrito');
    }
}