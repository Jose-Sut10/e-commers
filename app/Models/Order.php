<?php
namespace App\Models;
use Core\Model;
use Core\Database;

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
}