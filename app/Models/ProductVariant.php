<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class ProductVariant extends Model{
    protected string $table = 'product_variants';

    public static function forProduct(
        int $productId,
        bool $onlyActive = false
    ): array {
        $instance = new static();

        $sql = "
            SELECT *
            FROM `product_variants`
            WHERE `product_id` = ?
        ";

        if ($onlyActive) {
            $sql .= "
                AND `active` = 1
            ";
        }

        $sql .= "
            ORDER BY `name` ASC
        ";

        $rows = Database::select(
            $sql,
            [$productId]
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }

    public static function findBySku(
        string $sku
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `product_variants`
             WHERE `sku` = ?
             LIMIT 1",
            [
                strtoupper(
                    trim($sku)
                ),
            ]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }

    public function finalPrice(
        Product $product
    ): float {
        if (
            $this->price !== null
            && $this->price !== ''
        ) {
            return (float) $this->price;
        }

        return (float) $product->price;
    }

    public static function findForProduct(
        int $variantId,
        int $productId
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
            FROM `product_variants`
            WHERE `id` = ?
            AND `product_id` = ?
            LIMIT 1",
            [
                $variantId,
                $productId,
            ]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }

    public static function existsForProduct(
        int $productId
    ): bool {
        $row = Database::first(
            "SELECT `id`
            FROM `product_variants`
            WHERE `product_id` = ?
            LIMIT 1",
            [$productId]
        );
        return $row !== null;
    }
}