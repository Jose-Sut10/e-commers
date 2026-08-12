<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantController extends Controller{
    public function index(): void{
        $product = $this->findProduct();

        view(
            'product_variants/index',
            [
                'title' =>
                    'Variantes - '
                    . $product->name,

                'product' =>
                    $product,

                'variants' =>
                    ProductVariant::forProduct(
                        (int) $product->id
                    ),
            ]
        );
    }

    public function create(): void{
        $product = $this->findProduct();

        view(
            'product_variants/create',
            [
                'title' =>
                    'Nueva variante',

                'product' =>
                    $product,
            ]
        );
    }

    public function store(): void{
        $request = new Request();

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

        if (!$productId) {
            Session::flash(
                'warning',
                'El producto indicado no es válido.'
            );

            redirect('productos');
        }

        $product =
            Product::find(
                (int) $productId
            );

        if (!$product) {
            Session::flash(
                'warning',
                'El producto no existe.'
            );

            redirect('productos');
        }

        $name = trim(
            (string) (
                $input['name'] ?? ''
            )
        );

        $sku = strtoupper(
            trim(
                (string) (
                    $input['sku'] ?? ''
                )
            )
        );

        $price = trim(
            (string) (
                $input['price'] ?? ''
            )
        );

        $result = validator(
            $input,
            [
                'name' =>
                    'required|min:1|max:150',

                'sku' =>
                    'required|min:2|max:100',

                'price' =>
                    'numeric|min:0',
            ]
        )->validate();

        if (
            $result->first('sku') === null
            && ProductVariant::findBySku($sku)
        ) {
            $result->add(
                'sku',
                'Ya existe una variante con este SKU.'
            );
        }

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash(
                'old',
                [
                    'name' => $name,
                    'sku' => $sku,
                    'price' => $price,
                    'active' =>
                        isset($input['active'])
                            ? '1'
                            : '0',
                ]
            );

            redirect(
                'productos/variantes/crear?id='
                . (int) $product->id
            );
        }

        try {
            $variant =
                new ProductVariant([
                    'product_id' =>
                        (int) $product->id,

                    'name' =>
                        $name,

                    'sku' =>
                        $sku,

                    'price' =>
                        $price === ''
                            ? null
                            : (float) $price,

                    /*
                     * El stock siempre empieza en 0.
                     * Después lo administraremos
                     * desde Inventario.
                     */
                    'stock' =>
                        0,

                    'active' =>
                        isset($input['active'])
                            ? 1
                            : 0,
                ]);

            if (!$variant->save()) {
                throw new RuntimeException(
                    'No fue posible guardar la variante.'
                );
            }

            Session::flash(
                'success',
                'La variante fue registrada correctamente.'
            );

            redirect(
                'productos/variantes?id='
                . (int) $product->id
            );

        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'errors',
                [
                    'general' => [
                        'No fue posible registrar la variante.',
                    ],
                ]
            );

            redirect(
                'productos/variantes/crear?id='
                . (int) $product->id
            );
        }
    }

    private function findProduct(): Product{
        $id = filter_var(
            $_GET['id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$id) {
            Session::flash(
                'warning',
                'El producto indicado no es válido.'
            );

            redirect('productos');
        }

        $product =
            Product::find($id);

        if (!$product) {
            Session::flash(
                'warning',
                'El producto no fue encontrado.'
            );

            redirect('productos');
        }

        return $product;
    }
}