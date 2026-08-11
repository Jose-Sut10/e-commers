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
                users.name AS user_name
             FROM inventory_movements
             LEFT JOIN users
                ON users.id = inventory_movements.user_id
             WHERE inventory_movements.product_id = ?
             ORDER BY inventory_movements.id DESC",
            [$productId]
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }
}