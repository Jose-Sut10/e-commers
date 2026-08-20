<?php
namespace App\Models;
use Core\Model;
use Core\Database;
use Core\Pagination\Paginator;

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

    //paginacion
    public static function paginateAdmin(
        array $filters = [],
        int $page = 1,
        int $perPage = 15
    ): Paginator {

        $page =
            max(1, $page);

        $perPage =
            max(
                5,
                min(
                    $perPage,
                    100
                )
            );

        $sql = "
            FROM
            (
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

            ) AS customer_list

            WHERE 1 = 1
        ";

        $params = [];

        /*
        * BUSCAR
        */

        $search =
            trim(
                (string) (
                    $filters['q']
                    ?? ''
                )
            );

        if ($search !== '') {

            $sql .= "
                AND (
                    customer_list.name LIKE ?
                    OR customer_list.phone LIKE ?
                    OR customer_list.email LIKE ?
                )
            ";

            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }


        /*
        * ESTADO
        */

        $active =
            $filters['active']
            ?? '';

        if ( $active === '1' || $active === '0') {
            $sql .= "
                AND customer_list.active = ?
            ";

            $params[] =
                (int) $active;
        }

        /*
        * ACTIVIDAD
        */
        $orders =
            $filters['orders']
            ?? '';

        if ($orders === 'with') {

            $sql .= "
                AND customer_list.total_orders > 0
            ";

        } elseif ($orders === 'without') {

            $sql .= "
                AND customer_list.total_orders = 0
            ";
        }

        $count =
            Database::first(
                "
                SELECT COUNT(*) AS total
                {$sql}
                ",
                $params
            );

        $total =
            (int) (
                $count['total']
                ?? 0
            );

        $lastPage =
            max(
                1,
                (int) ceil(
                    $total / $perPage
                )
            );

        $page =
            min(
                $page,
                $lastPage
            );

        $offset =
            ($page - 1)
            * $perPage;

        $items =
            Database::select(
                "
                SELECT *
                {$sql}

                ORDER BY
                    customer_list.id DESC

                LIMIT {$perPage}
                OFFSET {$offset}
                ",
                $params
            );

        return new Paginator(
            $items,
            $total,
            $perPage,
            $page
        );
    }
}