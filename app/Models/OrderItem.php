<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class OrderItem extends Model{
    protected string $table = 'order_items';

    public static function forOrder(
        int $orderId
    ): array {
        $instance = new static();

        $rows = Database::select(
            "SELECT *
             FROM `order_items`
             WHERE `order_id` = ?
             ORDER BY `id` ASC",
            [$orderId]
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }
}