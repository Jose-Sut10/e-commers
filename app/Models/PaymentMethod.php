<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class PaymentMethod extends Model{
    protected string $table = 'payment_methods';

    public static function allOrdered(): array{
        $instance = new static();

        $rows =
            Database::select(
                "SELECT *
                 FROM `payment_methods`

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
                 FROM `payment_methods`

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
        $instance = new static();

        $row =
            Database::first(
                "SELECT *
                 FROM `payment_methods`

                 WHERE `id` = ?
                 AND `active` = 1

                 LIMIT 1",
                [
                    $id,
                ]
            );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }

    public static function findByCode(
        string $code
    ): ?static {
        $instance = new static();

        $row =
            Database::first(
                "SELECT *
                 FROM `payment_methods`

                 WHERE `code` = ?

                 LIMIT 1",
                [
                    strtolower(
                        trim($code)
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

    public static function findByCodeExceptId(
        string $code,
        int $id
    ): ?static {
        $instance =
            new static();

        $row =
            Database::first(
                "SELECT *
                 FROM `payment_methods`

                 WHERE `code` = ?
                 AND `id` != ?

                 LIMIT 1",
                [
                    strtolower(
                        trim($code)
                    ),

                    $id,
                ]
            );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }

    public function typeLabel(): string{
        return match (
            (string) $this->type
        ) {

            'cash' => 'Efectivo / Contra entrega',
            'bank_transfer' => 'Transferencia bancaria',
            'other' => 'Otro',
            default => 'Desconocido',
        };
    }
}