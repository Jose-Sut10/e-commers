<?php
namespace App\Models;
use Core\Model;
use Core\Database;
use Core\Pagination\Paginator;

class Order extends Model{
    protected string $table = 'orders';

    public static function allLatest(): array{
        $instance = new static();

        $rows = Database::select(
            "SELECT *
             FROM `orders`
             ORDER BY `id` DESC"
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }

    public static function findForUpdate(
        int $id
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `orders`
             WHERE `id` = ?
             LIMIT 1
             FOR UPDATE",
            [$id]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase($row);
    }

    public function statusLabel(): string{
        return match ($this->status) {
            'pending'   => 'Pendiente',
            'confirmed' => 'Confirmado',
            'shipped'   => 'Enviado',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
            default     => 'Desconocido',
        };
    }

    public function allowedTransitions(): array{
        return match ($this->status) {
            'pending' => [
                'confirmed',
                'cancelled',
            ],

            'confirmed' => [
                'shipped',
                'cancelled',
            ],

            'shipped' => [
                'delivered',
            ],

            default => [],
        };
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
            FROM orders
            WHERE 1 = 1
        ";

        $params = [];

        /*
        * BUSCADOR
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
                    number LIKE ?
                    OR customer_name LIKE ?
                    OR customer_phone LIKE ?
                    OR customer_email LIKE ?
                )
            ";

            $term =
                '%' . $search . '%';

            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        /*
        * ESTADO
        */

        $status =
            trim(
                (string) (
                    $filters['status']
                    ?? ''
                )
            );

        $allowedStatuses = [
            'pending',
            'confirmed',
            'shipped',
            'delivered',
            'cancelled',
        ];

        if (
            in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {

            $sql .= "
                AND status = ?
            ";

            $params[] =
                $status;
        }

        /*
        * FECHA DESDE
        */

        $dateFrom =
            trim(
                (string) (
                    $filters['date_from']
                    ?? ''
                )
            );

        if (
            $dateFrom !== ''
            && preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $dateFrom
            )
        ) {

            $sql .= "
                AND created_at >= ?
            ";

            $params[] =
                $dateFrom
                . ' 00:00:00';
        }

        /*
        * FECHA HASTA
        */

        $dateTo =
            trim(
                (string) (
                    $filters['date_to']
                    ?? ''
                )
            );

        if (
            $dateTo !== ''
            && preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $dateTo
            )
        ) {

            $sql .= "
                AND created_at <= ?
            ";

            $params[] =
                $dateTo
                . ' 23:59:59';
        }


        /*
        * CONTAR
        */

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

        /*
        * RESULTADOS
        */

        $rows =
            Database::select(
                "
                SELECT *
                {$sql}

                ORDER BY id DESC

                LIMIT {$perPage}
                OFFSET {$offset}
                ",
                $params
            );

        $instance =
            new static();

        $items =
            array_map(
                fn (array $row) =>
                    $instance->newFromDatabase(
                        $row
                    ),
                $rows
            );

        return new Paginator(
            $items,
            $total,
            $perPage,
            $page
        );
    }
}