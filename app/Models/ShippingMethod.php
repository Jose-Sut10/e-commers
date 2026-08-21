<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class ShippingMethod extends Model{
    protected string $table =
        'shipping_methods';


    public static function allOrdered(): array{
        $instance = new static();

        $rows =
            Database::select(
                "SELECT *
                 FROM `shipping_methods`

                 ORDER BY
                    `sort_order` ASC,
                    `id` ASC"
            );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase(
                    $row
                ),
            $rows
        );
    }

    public static function activeOrdered(): array{
        $instance = new static();

        $rows =
            Database::select(
                "SELECT *
                 FROM `shipping_methods`

                 WHERE `active` = 1

                 ORDER BY
                    `sort_order` ASC,
                    `id` ASC"
            );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase(
                    $row
                ),
            $rows
        );
    }


    public static function findActive(
        int $id
    ): ?static {
        $instance =
            new static();

        $row =
            Database::first(
                "SELECT *
                 FROM `shipping_methods`

                 WHERE `id` = ?
                 AND `active` = 1

                 LIMIT 1",
                [$id]
            );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase($row);
    }
}