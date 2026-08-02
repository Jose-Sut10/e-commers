<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class User extends Model{
    protected string $table = 'users';

    public static function findByEmail(string $email): ?static{
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `{$instance->table}`
             WHERE `email` = ?
             LIMIT 1",
            [
                mb_strtolower(trim($email)),
            ]
        );

        if (!$row) {
            return null;
        }
        return $instance->newFromDatabase($row);
    }
}