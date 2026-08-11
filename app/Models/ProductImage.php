<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class ProductImage extends Model{
    protected string $table = 'product_images';

    public static function forProduct(
        int $productId
    ): array {
        $instance = new static();

        $rows = Database::select(
            "SELECT *
             FROM `product_images`
             WHERE `product_id` = ?
             ORDER BY `is_primary` DESC, `id` ASC",
            [$productId]
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }

    public static function primaryForProduct(
        int $productId
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `product_images`
             WHERE `product_id` = ?
             ORDER BY `is_primary` DESC, `id` ASC
             LIMIT 1",
            [$productId]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase($row);
    }

    public static function clearPrimary(
        int $productId
    ): void {
        Database::execute(
            "UPDATE `product_images`
             SET `is_primary` = 0
             WHERE `product_id` = ?",
            [$productId]
        );
    }
}