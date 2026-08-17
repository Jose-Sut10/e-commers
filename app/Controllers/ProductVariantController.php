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
        $product =
            $this->findProductFromQuery();

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
        $product =
            $this->findProductFromQuery();

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
                $input['name']
                ?? ''
            )
        );

        $sku = strtoupper(
            trim(
                (string) (
                    $input['sku']
                    ?? ''
                )
            )
        );

        $price = trim(
            (string) (
                $input['price']
                ?? ''
            )
        );

        /*
         * IMPORTANTE:
         *
         * El precio es opcional.
         * Si está vacío usará el precio
         * principal del producto.
         */

        $validationData =
            $input;

        if ($price === '') {
            unset(
                $validationData['price']
            );
        }


        $rules = [
            'name' => 'required|min:1|max:150',
            'sku' => 'required|min:2|max:100',
        ];

        if ($price !== '') {
            $rules['price'] =
                'numeric|min:0';
        }

        $result = validator(
            $validationData,
            $rules
        )->validate();

        /** SKU duplicado en variantes*/

        if (
            $result->first('sku') === null
            && ProductVariant::findBySku(
                $sku
            )
        ) {
            $result->add(
                'sku',
                'Ya existe una variante con este SKU.'
            );
        }

        /*
         * También evitamos utilizar el
         * mismo SKU de un producto.
         */

        if (
            $result->first('sku') === null
            && Product::findBySku(
                $sku
            )
        ) {
            $result->add(
                'sku',
                'Este SKU ya está siendo utilizado por un producto.'
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
                    'name' =>  $name,
                    'sku' => $sku,
                    'price' => $price,
                    'active' =>
                        isset(
                            $input['active']
                        )
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
                    'name' => $name,
                    'sku' => $sku,
                    'price' => $price === ''
                            ? null
                            : (float) $price,
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

    /*
     * =====================================================
     * EDITAR
     * =====================================================
     */

    public function edit(): void{
        $product =
            $this->findProductFromQuery();


        $variantId = filter_var(
            $_GET['variant'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$variantId) {
            Session::flash(
                'warning',
                'La variante indicada no es válida.'
            );

            redirect(
                'productos/variantes?id='
                . (int) $product->id
            );
        }

        $variant =
            ProductVariant::findForProduct(
                (int) $variantId,
                (int) $product->id
            );

        if (!$variant) {
            Session::flash(
                'warning',
                'La variante no fue encontrada.'
            );

            redirect(
                'productos/variantes?id='
                . (int) $product->id
            );
        }

        view(
            'product_variants/edit',
            [
                'title' => 'Editar variante',
                'product' => $product,
                'variant' => $variant,
            ]
        );
    }

    /*
     * =====================================================
     * ACTUALIZAR
     * =====================================================
     */

    public function update(): void{
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

        $variantId = filter_var(
            $input['variant_id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (
            !$productId
            || !$variantId
        ) {
            Session::flash(
                'warning',
                'Los datos de la variante no son válidos.'
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

        $variant =
            ProductVariant::findForProduct(
                (int) $variantId,
                (int) $product->id
            );

        if (!$variant) {
            Session::flash(
                'warning',
                'La variante no fue encontrada.'
            );

            redirect(
                'productos/variantes?id='
                . (int) $product->id
            );
        }

        $name = trim(
            (string) (
                $input['name']
                ?? ''
            )
        );

        $sku = strtoupper(
            trim(
                (string) (
                    $input['sku']
                    ?? ''
                )
            )
        );

        $price = trim(
            (string) (
                $input['price']
                ?? ''
            )
        );

        $validationData =
            $input;

        if ($price === '') {
            unset(
                $validationData['price']
            );
        }

        $rules = [
            'name' => 'required|min:1|max:150',
            'sku' => 'required|min:2|max:100',
        ];

        if ($price !== '') {
            $rules['price'] =
                'numeric|min:0';
        }

        $result = validator(
            $validationData,
            $rules
        )->validate();

        /*
         * Verificar SKU contra
         * otras variantes.
         */

        if (
            $result->first('sku') === null
            && ProductVariant::findBySkuExceptId(
                $sku,
                (int) $variant->id
            )
        ) {
            $result->add(
                'sku',
                'Ya existe otra variante con este SKU.'
            );
        }

        /*
         * Verificar SKU contra productos.
         */

        if (
            $result->first('sku') === null
            && Product::findBySku(
                $sku
            )
        ) {
            $result->add(
                'sku',
                'Este SKU ya está siendo utilizado por un producto.'
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
                        isset(
                            $input['active']
                        )
                            ? '1'
                            : '0',
                ]
            );

            redirect(
                'productos/variantes/editar?id='
                . (int) $product->id
                . '&variant='
                . (int) $variant->id
            );
        }

        try {

            $variant->name = $name;
            $variant->sku = $sku;
            $variant->price =
                $price === ''
                    ? null
                    : (float) $price;
            $variant->active =
                isset($input['active'])
                    ? 1
                    : 0;

            if (!$variant->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar la variante.'
                );
            }

            Session::flash(
                'success',
                'La variante fue actualizada correctamente.'
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
                        'No fue posible actualizar la variante.',
                    ],
                ]
            );

            redirect(
                'productos/variantes/editar?id='
                . (int) $product->id
                . '&variant='
                . (int) $variant->id
            );
        }
    }

    /*
     * =====================================================
     * ELIMINAR
     * =====================================================
     */

    public function destroy(): void{
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

        $variantId = filter_var(
            $input['variant_id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$productId || !$variantId) {
            Session::flash(
                'warning',
                'Los datos de la variante no son válidos.'
            );

            redirect('productos');
        }

        $variant =
            ProductVariant::findForProduct(
                (int) $variantId,
                (int) $productId
            );

        if (!$variant) {
            Session::flash(
                'warning',
                'La variante no fue encontrada.'
            );

            redirect(
                'productos/variantes?id='
                . (int) $productId
            );
        }

        /*
         * No eliminamos una variante
         * que todavía tenga stock.
         */

        if ((int) $variant->stock > 0) {
            Session::flash(
                'warning',
                'No puedes eliminar esta variante porque todavía tiene existencias. Puedes desactivarla o dejar su stock en cero.'
            );

            redirect(
                'productos/variantes?id='
                . (int) $productId
            );
        }

        /*
         * Tampoco eliminamos historial.
         */

        if ($variant->hasOrderHistory()) {
            Session::flash(
                'warning',
                'Esta variante ya aparece en pedidos y no puede eliminarse. Puedes desactivarla.'
            );

            redirect(
                'productos/variantes?id='
                . (int) $productId
            );
        }

        if ($variant->hasInventoryHistory()) {

            Session::flash(
                'warning',
                'Esta variante tiene movimientos de inventario y no puede eliminarse. Puedes desactivarla.'
            );

            redirect(
                'productos/variantes?id='
                . (int) $productId
            );
        }

        try {
            $variant->delete();
            Session::flash(
                'success',
                'La variante fue eliminada correctamente.'
            );
        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'warning',
                'No fue posible eliminar la variante.'
            );
        }

        redirect(
            'productos/variantes?id='
            . (int) $productId
        );
    }

    /*
     * =====================================================
     * BUSCAR PRODUCTO DESDE GET
     * =====================================================
     */

    private function findProductFromQuery(): Product{
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
            Product::find(
                (int) $id
            );

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