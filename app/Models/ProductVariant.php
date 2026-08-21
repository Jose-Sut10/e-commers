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


    public static function findBySkuExceptId(
        string $sku,
        int $exceptId
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `product_variants`
             WHERE `sku` = ?
             AND `id` != ?
             LIMIT 1",
            [
                strtoupper(
                    trim($sku)
                ),

                $exceptId,
            ]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
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

    public static function findPublicForProduct(
        int $variantId,
        int $productId
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT
                product_variants.*

             FROM product_variants

             INNER JOIN products
                ON products.id =
                   product_variants.product_id

             INNER JOIN categories
                ON categories.id =
                   products.category_id

             WHERE product_variants.id = ?
             AND product_variants.product_id = ?
             AND product_variants.active = 1
             AND products.active = 1
             AND categories.active = 1

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

    public static function findPublicForUpdate(
        int $variantId,
        int $productId
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT
                product_variants.*

             FROM product_variants

             INNER JOIN products
                ON products.id =
                   product_variants.product_id

             INNER JOIN categories
                ON categories.id =
                   products.category_id

             WHERE product_variants.id = ?
             AND product_variants.product_id = ?
             AND product_variants.active = 1
             AND products.active = 1
             AND categories.active = 1

             LIMIT 1
             FOR UPDATE",
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

    public function basePrice(Product $product): float {
        if (
            $this->price !== null
            && $this->price !== ''
        ) {
            return (float) $this->price;
        }

        return (float) $product->price;
    }

    public function hasActiveSale(
        Product $product
    ): bool {
        if (
            $this->sale_price === null
            || $this->sale_price === ''
        ) {
            return false;
        }

        $regularPrice =
            $this->basePrice(
                $product
            );

        $salePrice = (float) $this->sale_price;

        if ($salePrice >= $regularPrice) {
            return false;
        }

        $now = time();

        if (
            $this->sale_starts_at
            && strtotime(
                (string)
                $this->sale_starts_at
            ) > $now
        ) {
            return false;
        }

        if (
            $this->sale_ends_at
            && strtotime(
                (string)
                $this->sale_ends_at
            ) < $now
        ) {
            return false;
        }

        return true;
    }

    public function finalPrice(
        Product $product
    ): float {
        /*
        * Primero tiene prioridad una
        * promoción propia de la variante.
        */

        if (
            $this->hasActiveSale(
                $product
            )
        ) {
            return (float)
                $this->sale_price;
        }

        /*
        * Si la variante tiene precio propio,
        * usamos ese precio.
        */

        if (
            $this->price !== null
            && $this->price !== ''
        ) {
            return (float)
                $this->price;
        }

        /*
        * Si no tiene precio propio,
        * puede aprovechar la promoción
        * general del producto.
        */

        return $product->finalPrice();
    }

    public function hasInventoryHistory(): bool{
        $row = Database::first(
            "SELECT `id`
             FROM `inventory_movements`
             WHERE `variant_id` = ?
             LIMIT 1",
            [
                (int) $this->id,
            ]
        );
        return $row !== null;
    }

    public function hasOrderHistory(): bool{
        $row = Database::first(
            "SELECT `id`
             FROM `order_items`
             WHERE `variant_id` = ?
             LIMIT 1",
            [
                (int) $this->id,
            ]
        );

        return $row !== null;
    }

    public function canDelete(): bool{
        if ((int) $this->stock > 0) {
            return false;
        }

        if ($this->hasInventoryHistory()) {
            return false;
        }

        if ($this->hasOrderHistory()) {
            return false;
        }
        return true;
    }
}