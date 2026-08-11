<?php
namespace App\Services;
use RuntimeException;
use Throwable;
use Core\Database;
use App\Models\Product;
use App\Models\InventoryMovement;

class InventoryService{
    public function add(
        Product $product,
        int $quantity,
        ?int $userId,
        ?string $reason = null
    ): InventoryMovement {
        return $this->move(
            $product,
            'in',
            $quantity,
            $userId,
            $reason
        );
    }

    public function remove(
        Product $product,
        int $quantity,
        ?int $userId,
        ?string $reason = null
    ): InventoryMovement {
        return $this->move(
            $product,
            'out',
            $quantity,
            $userId,
            $reason
        );
    }

    private function move(
        Product $product,
        string $type,
        int $quantity,
        ?int $userId,
        ?string $reason
    ): InventoryMovement {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'La cantidad debe ser mayor que cero.'
            );
        }

        if (!in_array(
            $type,
            ['in', 'out'],
            true
        )) {
            throw new RuntimeException(
                'El tipo de movimiento no es válido.'
            );
        }

        $previousStock =
            (int) $product->stock;

        if ($type === 'in') {
            $newStock =
                $previousStock + $quantity;
        } else {
            if ($quantity > $previousStock) {
                throw new RuntimeException(
                    'No hay existencias suficientes.'
                );
            }

            $newStock =
                $previousStock - $quantity;
        }

        Database::beginTransaction();

        try {
            $product->stock = $newStock;

            if (!$product->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar las existencias.'
                );
            }

            $movement = new InventoryMovement([
                'product_id' =>
                    (int) $product->id,

                'user_id' => $userId,

                'type' => $type,

                'quantity' => $quantity,

                'previous_stock' =>
                    $previousStock,

                'new_stock' =>
                    $newStock,

                'reason' =>
                    $reason === ''
                        ? null
                        : $reason,
            ]);

            if (!$movement->save()) {
                throw new RuntimeException(
                    'No fue posible registrar el movimiento.'
                );
            }

            Database::commit();

            return $movement;
        } catch (Throwable $exception) {
            if (Database::inTransaction()) {
                Database::rollBack();
            }

            throw $exception;
        }
    }
}