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
                categories.name AS category_name,

                (
                    SELECT product_images.path
                    FROM product_images
                    WHERE product_images.product_id = products.id
                    ORDER BY
                        product_images.is_primary DESC,
                        product_images.id ASC
                    LIMIT 1
                ) AS image_path

            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            ORDER BY products.id DESC"
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }

    //catalogo publico
    public static function publicCatalog(
        ?string $categorySlug = null,
        ?string $search = null
    ): array {
        $instance = new static();

        $sql = "
            SELECT
                products.*,

                categories.name AS category_name,
                categories.slug AS category_slug,

                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM product_variants pv
                        WHERE pv.product_id = products.id
                    )
                    THEN COALESCE(
                        (
                            SELECT SUM(pv2.stock)
                            FROM product_variants pv2
                            WHERE pv2.product_id = products.id
                            AND pv2.active = 1
                        ),
                        0
                    )
                    ELSE products.stock
                END AS available_stock,

                (
                    SELECT product_images.path
                    FROM product_images
                    WHERE product_images.product_id = products.id
                    ORDER BY
                        product_images.is_primary DESC,
                        product_images.id ASC
                    LIMIT 1
                ) AS image_path

            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.active = 1
            AND categories.active = 1
        ";

        $params = [];

        if (
            $categorySlug !== null
            && $categorySlug !== ''
        ) {
            $sql .= "
                AND categories.slug = ?
            ";

            $params[] = $categorySlug;
        }

        if (
            $search !== null
            && $search !== ''
        ) {
            $sql .= "
                AND (
                    products.name LIKE ?
                    OR products.sku LIKE ?
                    OR products.description LIKE ?
                )
            ";

            $term = '%' . $search . '%';

            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= "
            ORDER BY products.name ASC
        ";

        $rows = Database::select(
            $sql,
            $params
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }

    public static function findPublicBySlug(
        string $slug
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "
            SELECT
                products.*,

                categories.name AS category_name,
                categories.slug AS category_slug,

                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM product_variants pv
                        WHERE pv.product_id = products.id
                    )
                    THEN COALESCE(
                        (
                            SELECT SUM(pv2.stock)
                            FROM product_variants pv2
                            WHERE pv2.product_id = products.id
                            AND pv2.active = 1
                        ),
                        0
                    )
                    ELSE products.stock
                END AS available_stock,

                (
                    SELECT product_images.path
                    FROM product_images
                    WHERE product_images.product_id = products.id
                    ORDER BY
                        product_images.is_primary DESC,
                        product_images.id ASC
                    LIMIT 1
                ) AS image_path

            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.slug = ?
            AND products.active = 1
            AND categories.active = 1

            LIMIT 1
            ",
            [$slug]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }

    //carrito de compras
    public static function findPublicById(
        int $id
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "
            SELECT
                products.*,

                categories.name AS category_name,
                categories.slug AS category_slug,

                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM product_variants pv
                        WHERE pv.product_id = products.id
                    )
                    THEN COALESCE(
                        (
                            SELECT SUM(pv2.stock)
                            FROM product_variants pv2
                            WHERE pv2.product_id = products.id
                            AND pv2.active = 1
                        ),
                        0
                    )
                    ELSE products.stock
                END AS available_stock,

                (
                    SELECT product_images.path
                    FROM product_images
                    WHERE product_images.product_id = products.id
                    ORDER BY
                        product_images.is_primary DESC,
                        product_images.id ASC
                    LIMIT 1
                ) AS image_path

            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.id = ?
            AND products.active = 1
            AND categories.active = 1

            LIMIT 1
            ",
            [$id]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }

    //checkout y pedidos
    public static function findPublicForUpdate(int $id): ?static {
        $instance = new static();

        $row = Database::first(
            "
            SELECT products.*
            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.id = ?
            AND products.active = 1
            AND categories.active = 1

            LIMIT 1
            FOR UPDATE
            ",
            [$id]
        );

        if (!$row) {
            return null;
        }
        return $instance->newFromDatabase(
            $row
        );
    }
}