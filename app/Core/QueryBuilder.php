<?php

namespace Core;

use PDO;

class QueryBuilder
{
    protected PDO $db;

    protected string $table;

    protected array $where = [];

    protected string $orderBy = '';

    protected string $limit = '';

    protected array $bindings = [];

    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function where(string $column, mixed $value): static
    {
        $this->where[] = "{$column} = ?";

        $this->bindings[] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $direction = strtoupper($direction);

        if (!in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }

        $this->orderBy = " ORDER BY {$column} {$direction}";

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = " LIMIT {$limit}";

        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $sql .= $this->orderBy;
        $sql .= $this->limit;

        $stmt = $this->db->prepare($sql);

        $stmt->execute($this->bindings);

        return $stmt->fetchAll();
    }
}