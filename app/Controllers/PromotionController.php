<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\PromotionService;

class PromotionController extends Controller{
    public function show(): void {
        $product = $this->findProductFromQuery();

        view(
            'promotions/show',
            [
                'title' =>
                    'Promociones - '
                    . $product->name,

                'product' => $product,

                'variants' =>
                    ProductVariant::forProduct(
                        (int) $product->id
                    ),
            ]
        );
    }

    public function updateProduct(): void{
        $request = new Request();
        $input = $request->all();

        $productId =
            $this->validId(
                $input['product_id']
                ?? null
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
                $productId
            );

        if (!$product) {
            Session::flash(
                'warning',
                'El producto no fue encontrado.'
            );

            redirect('productos');
        }

        try {

            (
                new PromotionService()
            )->updateProduct(
                $product,
                $input
            );

            Session::flash(
                'success',
                'La promoción del producto fue actualizada correctamente.'
            );

        } catch (RuntimeException $exception) {

            Session::flash(
                'warning',
                $exception->getMessage()
            );

        } catch (Throwable $exception) {
            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'warning',
                'No fue posible actualizar la promoción.'
            );
        }

        redirect(
            'productos/promocion?id='
            . (int) $product->id
        );
    }

    public function updateVariant(): void{
        $request = new Request();
        $input = $request->all();

        $productId =
            $this->validId(
                $input['product_id']
                ?? null
            );

        $variantId =
            $this->validId(
                $input['variant_id']
                ?? null
            );

        if (!$productId || !$variantId) {
            Session::flash(
                'warning',
                'Los datos de la promoción no son válidos.'
            );

            redirect('productos');
        }

        $product =
            Product::find(
                $productId
            );

        if (!$product) {
            Session::flash(
                'warning',
                'El producto no fue encontrado.'
            );
            redirect('productos');
        }

        $variant =
            ProductVariant::findForProduct(
                $variantId,
                $productId
            );

        if (!$variant) {
            Session::flash(
                'warning',
                'La variante no fue encontrada.'
            );

            redirect(
                'productos/promocion?id='
                . $productId
            );
        }

        try {

            (
                new PromotionService()
            )->updateVariant(
                $variant,
                $product,
                $input
            );

            Session::flash(
                'success',
                'La promoción de la variante fue actualizada correctamente.'
            );

        } catch (RuntimeException $exception) {

            Session::flash(
                'warning',
                $exception->getMessage()
            );

        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'warning',
                'No fue posible actualizar la promoción.'
            );
        }

        redirect(
            'productos/promocion?id='
            . $productId
        );
    }

    private function findProductFromQuery(): Product{
        $id =
            $this->validId(
                $_GET['id']
                ?? null
            );


        if (!$id) {
            Session::flash(
                'warning',
                'El producto indicado no es válido.'
            );

            redirect('productos');
        }

        $product = Product::find($id);

        if (!$product) {
            Session::flash(
                'warning',
                'El producto no fue encontrado.'
            );

            redirect('productos');
        }

        return $product;
    }

    private function validId(
        mixed $value
    ): ?int {
        $id = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        return $id
            ? (int) $id
            : null;
    }
}