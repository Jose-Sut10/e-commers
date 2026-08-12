<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class InventoryMovement extends Model{
    protected string $table = 'inventory_movements';

    public static function forProduct(
        int $productId
    ): array {
        $instance = new static();

        $rows = Database::select(
            "SELECT
                inventory_movements.*,

                users.name
                    AS user_name,

                product_variants.name
                    AS variant_name,

                product_variants.sku
                    AS variant_sku

             FROM inventory_movements

             LEFT JOIN users
                ON users.id =
                   inventory_movements.user_id

             LEFT JOIN product_variants
                ON product_variants.id =
                   inventory_movements.variant_id

             WHERE
                inventory_movements.product_id = ?

             ORDER BY
                inventory_movements.id DESC",
            [$productId]
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }
}