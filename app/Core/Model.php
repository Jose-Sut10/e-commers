<?php

namespace Core;

use PDO;
use BadMethodCallException;

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

    public static function __callStatic($method, $arguments)
    {
        $instance = new static();

        if (!method_exists($instance, $method)) {
            return $instance->query()->$method(...$arguments);
        }

        return $instance->$method(...$arguments);
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