<?php
namespace App\Services;
use Throwable;
use RuntimeException;
use Core\Database;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;

class InventoryService{
    /*
     * =====================================================
     * PRODUCTO SIN VARIANTES
     * =====================================================
     */

    public function add(
        Product $product,
        int $quantity,
        ?int $userId,
        ?string $reason = null
    ): InventoryMovement {
        return $this->moveProduct(
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
        return $this->moveProduct(
            $product,
            'out',
            $quantity,
            $userId,
            $reason
        );
    }

    /*
     * =====================================================
     * VARIANTES
     * =====================================================
     */

    public function addVariant(
        ProductVariant $variant,
        int $quantity,
        ?int $userId,
        ?string $reason = null
    ): InventoryMovement {
        return $this->moveVariant(
            $variant,
            'in',
            $quantity,
            $userId,
            $reason
        );
    }

    public function removeVariant(
        ProductVariant $variant,
        int $quantity,
        ?int $userId,
        ?string $reason = null
    ): InventoryMovement {
        return $this->moveVariant(
            $variant,
            'out',
            $quantity,
            $userId,
            $reason
        );
    }


    /*
     * =====================================================
     * MOVIMIENTO DE PRODUCTO
     * =====================================================
     */

    private function moveProduct(
        Product $product,
        string $type,
        int $quantity,
        ?int $userId,
        ?string $reason
    ): InventoryMovement {
        if (
            ProductVariant::existsForProduct(
                (int) $product->id
            )
        ) {
            throw new RuntimeException(
                'Este producto utiliza variantes. Debes registrar el movimiento en una variante específica.'
            );
        }

        $previousStock =
            (int) $product->stock;

        $newStock =
            $this->calculateStock(
                $previousStock,
                $quantity,
                $type
            );

        return $this->executeMovement(
            target: $product,
            productId: (int) $product->id,
            variantId: null,
            type: $type,
            quantity: $quantity,
            previousStock: $previousStock,
            newStock: $newStock,
            userId: $userId,
            reason: $reason
        );
    }


    /*
     * =====================================================
     * MOVIMIENTO DE VARIANTE
     * =====================================================
     */

    private function moveVariant(
        ProductVariant $variant,
        string $type,
        int $quantity,
        ?int $userId,
        ?string $reason
    ): InventoryMovement {
        $previousStock =
            (int) $variant->stock;

        $newStock =
            $this->calculateStock(
                $previousStock,
                $quantity,
                $type
            );

        return $this->executeMovement(
            target: $variant,
            productId: (int) $variant->product_id,
            variantId: (int) $variant->id,
            type: $type,
            quantity: $quantity,
            previousStock: $previousStock,
            newStock: $newStock,
            userId: $userId,
            reason: $reason
        );
    }


    /*
     * =====================================================
     * CÁLCULO
     * =====================================================
     */

    private function calculateStock(
        int $previousStock,
        int $quantity,
        string $type
    ): int {
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

        if ($type === 'in') {
            return $previousStock
                + $quantity;
        }

        if ($quantity > $previousStock) {
            throw new RuntimeException(
                'No hay existencias suficientes.'
            );
        }

        return $previousStock
            - $quantity;
    }

    /*
     * =====================================================
     * GUARDAR MOVIMIENTO
     * =====================================================
     */

    private function executeMovement(
        Product|ProductVariant $target,
        int $productId,
        ?int $variantId,
        string $type,
        int $quantity,
        int $previousStock,
        int $newStock,
        ?int $userId,
        ?string $reason
    ): InventoryMovement {

        $ownsTransaction =
            !Database::inTransaction();

        if ($ownsTransaction) {
            Database::beginTransaction();
        }

        try {
            $target->stock =
                $newStock;

            if (!$target->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar las existencias.'
                );
            }

            $movement =
                new InventoryMovement([
                    'product_id' =>
                        $productId,

                    'variant_id' =>
                        $variantId,

                    'user_id' =>
                        $userId,

                    'type' =>
                        $type,

                    'quantity' =>
                        $quantity,

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

            if ($ownsTransaction) {
                Database::commit();
            }

            return $movement;

        } catch (Throwable $exception) {

            if (
                $ownsTransaction
                && Database::inTransaction()
            ) {
                Database::rollBack();
            }

            throw $exception;
        }
    }
}