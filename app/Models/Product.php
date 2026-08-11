<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class Product extends Model{
    protected string $table = 'products';

    public static function findBySlug(
        string $slug
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `products`
             WHERE `slug` = ?
             LIMIT 1",
            [$slug]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase($row);
    }

    public static function findBySku(
        string $sku
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `products`
             WHERE `sku` = ?
             LIMIT 1",
            [trim($sku)]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase($row);
    }

    public static function allWithCategory(): array{
        $instance = new static();

        $rows = Database::select(
            "SELECT
                products.*,
                categories.name AS category_name
             FROM `products`
             INNER JOIN `categories`
                ON categories.id = products.category_id
             ORDER BY products.id DESC"
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }
}