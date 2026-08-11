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

class InventoryController extends Controller
{
    public function show(): void
    {
        $product = $this->findProduct();

        view('inventory/show', [
            'title' =>
                'Inventario - '
                . $product->name,

            'product' => $product,

            'movements' =>
                InventoryMovement::forProduct(
                    (int) $product->id
                ),
        ]);
    }

    public function store(): void
    {
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

        $product = Product::find(
            $productId
        );

        if (!$product) {
            Session::flash(
                'warning',
                'El producto no existe.'
            );

            redirect('productos');
        }

        $type = (string) (
            $input['type'] ?? ''
        );

        $quantity = filter_var(
            $input['quantity'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        $reason = trim(
            (string) (
                $input['reason'] ?? ''
            )
        );

        $result = validator($input, [
            'type' =>
                'required',

            'quantity' =>
                'required|numeric|min:1',

            'reason' =>
                'max:255',
        ])->validate();

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

        if (
            $type === 'out'
            && $quantity !== false
            && $quantity > (int) $product->stock
        ) {
            $result->add(
                'quantity',
                'No hay existencias suficientes para realizar esta salida.'
            );
        }

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash('old', [
                'type' => $type,
                'quantity' =>
                    $input['quantity'] ?? '',
                'reason' => $reason,
            ]);

            redirect(
                'inventario?id='
                . (int) $product->id
            );
        }

        try {
            $service =
                new InventoryService();

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

            Session::flash(
                'success',
                'El movimiento de inventario fue registrado correctamente.'
            );

            redirect(
                'inventario?id='
                . (int) $product->id
            );
        } catch (Throwable $exception) {
            error_log(
                $exception->getMessage()
            );

            Session::flash('errors', [
                'general' => [
                    $exception instanceof RuntimeException
                        ? $exception->getMessage()
                        : 'No fue posible registrar el movimiento.',
                ],
            ]);

            redirect(
                'inventario?id='
                . (int) $product->id
            );
        }
    }

    private function findProduct(): Product
    {
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