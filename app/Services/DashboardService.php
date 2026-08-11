<?php
namespace App\Services;
use Core\Database;
class DashboardService{
    public function statistics(): array{
        return [
            'products' => $this->productStats(),
            'orders' => $this->orderStats(),
        ];
    }

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

    public function lowStockProducts(
        int $limit = 5,
        int $threshold = 5
    ): array {
        $limit = max(
            1,
            min($limit, 20)
        );

        $threshold = max(
            0,
            $threshold
        );

        return Database::select(
            "
            SELECT
                products.id,
                products.name,
                products.sku,
                products.stock,
                categories.name AS category_name
            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.active = 1
            AND products.stock <= ?

            ORDER BY products.stock ASC,
                     products.name ASC

            LIMIT {$limit}
            ",
            [$threshold]
        );
    }

    private function productStats(): array{
        $row = Database::first(
            "
            SELECT
                COUNT(*) AS total,

                SUM(
                    CASE
                        WHEN active = 1
                        THEN 1
                        ELSE 0
                    END
                ) AS active,

                SUM(
                    CASE
                        WHEN active = 1
                        AND stock <= 5
                        THEN 1
                        ELSE 0
                    END
                ) AS low_stock

            FROM products
            "
        );

        return [
            'total' =>
                (int) ($row['total'] ?? 0),

            'active' =>
                (int) ($row['active'] ?? 0),

            'low_stock' =>
                (int) ($row['low_stock'] ?? 0),
        ];
    }

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
                            AND DATE(created_at) = CURDATE()
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
                (int) ($row['total'] ?? 0),

            'pending' =>
                (int) ($row['pending'] ?? 0),

            'sales' =>
                (float) ($row['sales'] ?? 0),

            'today_sales' =>
                (float) ($row['today_sales'] ?? 0),
        ];
    }
}