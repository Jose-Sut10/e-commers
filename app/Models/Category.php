<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class Category extends Model{
    protected string $table = 'categories';

    public static function findBySlug(
        string $slug
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `{$instance->table}`
             WHERE `slug` = ?
             LIMIT 1",
            [$slug]
        );

        if (!$row) {
            return null;
        }
        return $instance->newFromDatabase($row);
    }

    //registro y listado de productos
    public static function active(): array{
        $instance = new static();

        $rows = Database::select(
            "SELECT *
            FROM `categories`
            WHERE `active` = 1
            ORDER BY `name` ASC"
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }
}