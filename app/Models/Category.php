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
}