<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class Customer extends Model{
    protected string $table = 'customers';

    public static function findByPhone(
        string $phone
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `customers`
             WHERE `phone` = ?
             LIMIT 1",
            [
                trim($phone),
            ]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }


    public static function allWithStats(): array{
        return Database::select(
            "
            SELECT
                customers.*,

                COUNT(orders.id)
                    AS total_orders,

                COALESCE(
                    SUM(
                        CASE
                            WHEN orders.status IN (
                                'confirmed',
                                'shipped',
                                'delivered'
                            )
                            THEN orders.total
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_spent,

                MAX(orders.created_at)
                    AS last_order_at

            FROM customers

            LEFT JOIN orders
                ON orders.customer_id =
                   customers.id

            GROUP BY customers.id

            ORDER BY
                customers.id DESC
            "
        );
    }

    public static function findWithStats(
        int $id
    ): ?array {
        return Database::first(
            "
            SELECT
                customers.*,

                COUNT(orders.id)
                    AS total_orders,

                COALESCE(
                    SUM(
                        CASE
                            WHEN orders.status IN (
                                'confirmed',
                                'shipped',
                                'delivered'
                            )
                            THEN orders.total
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_spent,

                MAX(orders.created_at)
                    AS last_order_at

            FROM customers

            LEFT JOIN orders
                ON orders.customer_id =
                   customers.id

            WHERE customers.id = ?

            GROUP BY customers.id

            LIMIT 1
            ",
            [$id]
        );
    }


    public static function orders(
        int $customerId
    ): array {
        return Database::select(
            "
            SELECT *
            FROM orders

            WHERE customer_id = ?

            ORDER BY id DESC
            ",
            [$customerId]
        );
    }
}