<?php

namespace Core;

use PDO;
use BadMethodCallException;
use Core\Query\Builder;

abstract class Model{
    protected PDO $db;
    protected string $table;

    public function __construct(){
        $this->db = Database::connect();
    }

    public static function query(): Builder{
        $instance = new static();

        return (new Builder())
            ->table($instance->table)
            ->model(static::class);
    }

    public static function __callStatic($method, $arguments){
        $instance = new static();

        if (!method_exists($instance, $method)) {
            return $instance->query()->$method(...$arguments);
        }
        return $instance->$method(...$arguments);
    }

    public function all(): array{
        return $this->query()->get();
    }

    public static function where(
        string $column,
        mixed $value
    ): Builder {
        return static::query()
            ->where(
                $column,
                $value
            );
    }
}