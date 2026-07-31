<?php

namespace Core;

use PDO;

abstract class Model
{
    protected PDO $db;

    protected string $table;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    protected function query(): QueryBuilder
    {
        return new QueryBuilder(
            $this->db,
            $this->table
        );
    }

    public function all(): array
    {
        return $this->query()->get();
    }

    public function where(string $column, mixed $value): QueryBuilder
    {
        return $this->query()->where($column, $value);
    }
}