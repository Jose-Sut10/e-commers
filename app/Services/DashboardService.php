<?php
namespace App\Services;
use Core\Database;

class DashboardService{
    /*
     * =====================================================
     * ESTADÍSTICAS GENERALES
     * =====================================================
     */

    public function statistics(): array{
        return [
            'products' => [
                'total' =>
                    $this->totalProducts(),

                'active' =>
                    $this->activeProducts(),

                'low_stock' =>
                    $this->lowStockCount(),
            ],

            'orders' =>
                $this->orderStats(),
        ];
    }

    /*
     * =====================================================
     * PEDIDOS RECIENTES
     * =====================================================
     */

    public function recentOrders(
        int $limit = 5
    ): array {
        $limit = max(
            1,
            min($limit, 20)
        );

        return Database::select(
            "
            SELECT
                id,
                number,
                customer_name,
                customer_phone,
                total,
                status,
                created_at

            FROM orders

            ORDER BY id DESC

            LIMIT {$limit}
            "
        );
    }


    /*
     * =====================================================
     * STOCK BAJO
     * =====================================================
     *
     * Incluye:
     *
     * 1. Productos que NO tienen variantes.
     * 2. Variantes activas.
     *
     * Si un producto tiene variantes,
     * dejamos de utilizar products.stock.
     * =====================================================
     */

    public function lowStockProducts(
        int $limit = 10,
        int $threshold = 5
    ): array {
        $limit = max(
            1,
            min($limit, 50)
        );

        $threshold = max(
            0,
            $threshold
        );

        return Database::select(
            "
            (
                SELECT

                    products.id
                        AS product_id,

                    products.name
                        AS product_name,

                    NULL
                        AS variant_id,

                    NULL
                        AS variant_name,

                    products.sku
                        AS sku,

                    products.stock
                        AS stock,

                    categories.name
                        AS category_name,

                    'product'
                        AS stock_type

                FROM products

                INNER JOIN categories
                    ON categories.id =
                       products.category_id

                WHERE products.active = 1
                AND categories.active = 1

                AND products.stock <= ?

                AND NOT EXISTS (
                    SELECT 1

                    FROM product_variants

                    WHERE product_variants.product_id =
                          products.id
                )
            )

            UNION ALL

            (
                SELECT

                    products.id
                        AS product_id,

                    products.name
                        AS product_name,

                    product_variants.id
                        AS variant_id,

                    product_variants.name
                        AS variant_name,

                    product_variants.sku
                        AS sku,

                    product_variants.stock
                        AS stock,

                    categories.name
                        AS category_name,

                    'variant'
                        AS stock_type

                FROM product_variants

                INNER JOIN products
                    ON products.id =
                       product_variants.product_id

                INNER JOIN categories
                    ON categories.id =
                       products.category_id

                WHERE product_variants.active = 1

                AND products.active = 1

                AND categories.active = 1

                AND product_variants.stock <= ?
            )

            ORDER BY
                stock ASC,
                product_name ASC

            LIMIT {$limit}
            ",
            [
                $threshold,
                $threshold,
            ]
        );
    }

    /*
     * =====================================================
     * TOTAL DE PRODUCTOS
     * =====================================================
     */

    private function totalProducts(): int{
        $row = Database::first(
            "
            SELECT COUNT(*) AS total
            FROM products
            "
        );

        return (int) (
            $row['total']
            ?? 0
        );
    }

    /*
     * =====================================================
     * PRODUCTOS ACTIVOS
     * =====================================================
     */

    private function activeProducts(): int{
        $row = Database::first(
            "
            SELECT COUNT(*) AS total

            FROM products

            WHERE active = 1
            "
        );

        return (int) (
            $row['total']
            ?? 0
        );
    }


    /*
     * =====================================================
     * CANTIDAD DE EXISTENCIAS BAJAS
     * =====================================================
     */

    private function lowStockCount(
        int $threshold = 5
    ): int {
        $threshold = max(
            0,
            $threshold
        );


        /*
         * Productos normales
         */

        $products =
            Database::first(
                "
                SELECT COUNT(*) AS total

                FROM products

                INNER JOIN categories
                    ON categories.id =
                       products.category_id

                WHERE products.active = 1

                AND categories.active = 1

                AND products.stock <= ?

                AND NOT EXISTS (
                    SELECT 1

                    FROM product_variants

                    WHERE product_variants.product_id =
                          products.id
                )
                ",
                [$threshold]
            );


        /*
         * Variantes
         */

        $variants =
            Database::first(
                "
                SELECT COUNT(*) AS total

                FROM product_variants

                INNER JOIN products
                    ON products.id =
                       product_variants.product_id

                INNER JOIN categories
                    ON categories.id =
                       products.category_id

                WHERE product_variants.active = 1

                AND products.active = 1

                AND categories.active = 1

                AND product_variants.stock <= ?
                ",
                [$threshold]
            );

        return
            (int) (
                $products['total']
                ?? 0
            )
            +
            (int) (
                $variants['total']
                ?? 0
            );
    }

    /*
     * =====================================================
     * ESTADÍSTICAS DE PEDIDOS
     * =====================================================
     */

    private function orderStats(): array{
        $row = Database::first(
            "
            SELECT
                COUNT(*) AS total,
                SUM(
                    CASE
                        WHEN status = 'pending'
                        THEN 1
                        ELSE 0
                    END
                ) AS pending,

                COALESCE(
                    SUM(
                        CASE
                            WHEN status IN (
                                'confirmed',
                                'shipped',
                                'delivered'
                            )
                            THEN total
                            ELSE 0
                        END
                    ),
                    0
                ) AS sales,

                COALESCE(
                    SUM(
                        CASE
                            WHEN status IN (
                                'confirmed',
                                'shipped',
                                'delivered'
                            )

                            AND DATE(created_at)
                                = CURDATE()

                            THEN total
                            ELSE 0
                        END
                    ),
                    0
                ) AS today_sales
            FROM orders
            "
        );

        return [
            'total' =>
                (int) (
                    $row['total']
                    ?? 0
                ),

            'pending' =>
                (int) (
                    $row['pending']
                    ?? 0
                ),

            'sales' =>
                (float) (
                    $row['sales']
                    ?? 0
                ),

            'today_sales' =>
                (float) (
                    $row['today_sales']
                    ?? 0
                ),
        ];
    }
}