<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class Coupon extends Model{
    protected string $table = 'coupons';

    public static function allLatest(): array{
        $instance = new static();

        $rows =
            Database::select(
                "SELECT *
                 FROM `coupons`
                 ORDER BY `id` DESC"
            );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase(
                    $row
                ),
            $rows
        );
    }

    public static function findByCode(
        string $code
    ): ?static {
        $instance = new static();

        $row =
            Database::first(
                "SELECT *
                 FROM `coupons`
                 WHERE `code` = ?
                 LIMIT 1",
                [
                    strtoupper(
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
                 FROM `coupons`
                 WHERE `code` = ?
                 AND `id` != ?
                 LIMIT 1",
                [
                    strtoupper(
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

    public static function findForUpdateByCode(
        string $code
    ): ?static {
        $instance =
            new static();

        $row =
            Database::first(
                "SELECT *
                 FROM `coupons`

                 WHERE `code` = ?

                 LIMIT 1
                 FOR UPDATE",
                [
                    strtoupper(
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

    public function typeLabel(): string{
        return match ($this->type) {

            'percentage' =>
                'Porcentaje',

            'fixed' =>
                'Monto fijo',

            default =>
                'Desconocido',
        };
    }
}