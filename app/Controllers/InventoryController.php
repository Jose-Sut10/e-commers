<?php

namespace App\Controllers;

use Throwable;
use RuntimeException;
use Core\Auth\Auth;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Product;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use App\Models\ProductVariant;

class InventoryController extends Controller{
    public function show(): void{
        $product =
            $this->findProduct();

        $variants =
            ProductVariant::forProduct(
                (int) $product->id
            );

        view('inventory/show', [
            'title' =>
                'Inventario - '
                . $product->name,

            'product' =>
                $product,

            'variants' =>
                $variants,

            'movements' =>
                InventoryMovement::forProduct(
                    (int) $product->id
                ),
        ]);
    }

    public function store(): void{
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


        /*
        * VARIANTES DEL PRODUCTO
        */

        $variants =
            ProductVariant::forProduct(
                (int) $product->id
            );

        $hasVariants =
            !empty($variants);


        /*
        * VARIANTE SELECCIONADA
        */

        $variant = null;

        if ($hasVariants) {

            $variantId = filter_var(
                $input['variant_id'] ?? null,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                    ],
                ]
            );

            if (!$variantId) {
                Session::flash(
                    'errors',
                    [
                        'variant_id' => [
                            'Debes seleccionar una variante.'
                        ],
                    ]
                );

                Session::flash(
                    'old',
                    $input
                );

                redirect(
                    'inventario?id='
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
                    'La variante seleccionada no pertenece a este producto.'
                );

                redirect(
                    'inventario?id='
                    . (int) $product->id
                );
            }
        }

        /** TIPO*/

        $type = trim(
            (string) (
                $input['type'] ?? ''
            )
        );

        /** CANTIDAD*/

        $quantity = filter_var(
            $input['quantity'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        /** MOTIVO*/

        $reason = trim(
            (string) (
                $input['reason'] ?? ''
            )
        );

        /** VALIDACIÓN*/

        $result = validator(
            $input,
            [
                'type' =>
                    'required',

                'quantity' =>
                    'required|numeric|min:1',

                'reason' =>
                    'max:255',
            ]
        )->validate();


        if (!in_array(
            $type,
            ['in', 'out'],
            true
        )) {
            $result->add(
                'type',
                'Selecciona un tipo de movimiento válido.'
            );
        }

        if ($quantity === false) {
            $result->add(
                'quantity',
                'La cantidad debe ser un número entero mayor que cero.'
            );
        }

        /** VALIDAR STOCK DE SALIDA */

        if (
            $type === 'out'
            && $quantity !== false
        ) {

            $availableStock =
                $hasVariants
                    ? (int) $variant->stock
                    : (int) $product->stock;

            if (
                $quantity
                > $availableStock
            ) {
                $result->add(
                    'quantity',
                    'No hay existencias suficientes para realizar esta salida.'
                );
            }
        }

        if ($result->fails()) {

            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash(
                'old',
                [
                    'variant_id' =>
                        $input['variant_id']
                        ?? '',

                    'type' =>
                        $type,

                    'quantity' =>
                        $input['quantity']
                        ?? '',

                    'reason' =>
                        $reason,
                ]
            );

            redirect(
                'inventario?id='
                . (int) $product->id
            );
        }

        /** REGISTRAR MOVIMIENTO*/

        try {

            $service =
                new InventoryService();


            if ($hasVariants) {

                if ($type === 'in') {

                    $service->addVariant(
                        $variant,
                        (int) $quantity,
                        Auth::id(),
                        $reason
                    );

                } else {

                    $service->removeVariant(
                        $variant,
                        (int) $quantity,
                        Auth::id(),
                        $reason
                    );
                }

            } else {

                if ($type === 'in') {

                    $service->add(
                        $product,
                        (int) $quantity,
                        Auth::id(),
                        $reason
                    );

                } else {

                    $service->remove(
                        $product,
                        (int) $quantity,
                        Auth::id(),
                        $reason
                    );
                }
            }

            Session::flash(
                'success',
                'El movimiento de inventario fue registrado correctamente.'
            );

        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'errors',
                [
                    'general' => [
                        $exception->getMessage(),
                    ],
                ]
            );
        }

        redirect(
            'inventario?id='
            . (int) $product->id
        );
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
}