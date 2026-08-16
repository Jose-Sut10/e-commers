<?php
namespace App\Controllers;
use Throwable;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Product;
use App\Models\Company;
use App\Services\CartService;
use App\Models\ProductVariant;

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

        if (!$productId || !$quantity) {
            Session::flash(
                'warning',
                'Los datos del producto no son válidos.'
            );
            redirect('tienda');
        }

        $product = Product::findPublicById($productId);

        $variantId = filter_var(
            $input['variant_id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        $variant = null;

        if ($variantId) {
            $variant =
                ProductVariant::findPublicForProduct(
                    (int) $variantId,
                    (int) $product->id
                );

            if (!$variant) {
                Session::flash(
                    'warning',
                    'La variante seleccionada no está disponible.'
                );

                redirect(
                    'producto?slug='
                    . urlencode(
                        (string) $product->slug
                    )
                );
            }
        }

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
                $quantity,
                $variant
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
        $request =
            new Request();

        $input =
            $request->all();

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
            $input['quantity'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 0,
                ],
            ]
        );

        /*
        * La variante es opcional.
        * Si viene vacía significa que
        * es un producto sin variantes.
        */
        $variantId = null;

        if (
            isset($input['variant_id'])
            && $input['variant_id'] !== ''
        ) {
            $variantValue = filter_var(
                $input['variant_id'],
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                    ],
                ]
            );

            if (!$variantValue) {
                Session::flash(
                    'warning',
                    'La variante indicada no es válida.'
                );

                redirect('carrito');
            }

            $variantId =
                (int) $variantValue;
        }

        if (
            !$productId
            || $quantity === false
        ) {
            Session::flash(
                'warning',
                'Los datos del carrito no son válidos.'
            );

            redirect('carrito');
        }

        try {
            $cart =
                new CartService();

            $cart->update(
                (int) $productId,
                $variantId,
                (int) $quantity
            );

            Session::flash(
                'success',
                'El carrito fue actualizado correctamente.'
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
        $request =
            new Request();

        $input =
            $request->all();

        $productId = filter_var(
            $input['product_id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        /*
        * La variante es opcional.
        */
        $variantId = null;

        if (
            isset($input['variant_id'])
            && $input['variant_id'] !== ''
        ) {
            $variantValue = filter_var(
                $input['variant_id'],
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                    ],
                ]
            );

            if (!$variantValue) {
                Session::flash(
                    'warning',
                    'La variante indicada no es válida.'
                );

                redirect('carrito');
            }

            $variantId =
                (int) $variantValue;
        }

        if (!$productId) {
            Session::flash(
                'warning',
                'El producto indicado no es válido.'
            );

            redirect('carrito');
        }

        try {
            $cart =
                new CartService();

            $cart->remove(
                (int) $productId,
                $variantId
            );

            Session::flash(
                'success',
                'El producto fue eliminado del carrito.'
            );

        } catch (Throwable $exception) {

            Session::flash(
                'warning',
                $exception->getMessage()
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