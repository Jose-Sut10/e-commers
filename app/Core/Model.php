<?php

namespace Core;

use PDO;

abstract class Model
{
    protected PDO $db;

    protected string $table;

    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        return $this->db
            ->query($sql)
            ->fetchAll();
    }

    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table}
                WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}