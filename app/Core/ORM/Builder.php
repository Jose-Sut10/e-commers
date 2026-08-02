<?php
namespace Core\ORM;
use Core\Database;
use Core\ORM\Hydrator;

class Builder
{
    protected string $model;
    protected string $table;
    protected array $select = ['*'];
    protected array $wheres = [];
    protected ?string $orderBy = null;
    protected ?int $limit = null;

    public function table(string $table): static{
        $this->table = $table;
        return $this;
    }

    public function select(string ...$columns): static{
        $this->select = $columns;
        return $this;
    }

    public function where(
        string $column,
        mixed $operator,
        mixed $value = null
        ): static{
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'boolean' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function orWhere(
        string $column,
        mixed $operator,
        mixed $value = null
        ): static{
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'boolean' => 'OR',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function orderBy(
        string $column,
        string $direction='ASC'
    ): static {
        $this->orderBy =
            "{$column} {$direction}";
        return $this;
    }

    public function orderByDesc(
        string $column
        ): static{
        return $this->orderBy(
            $column,
            'DESC'
        );
    }

    public function first(): ?object{
        $this->limit = 1;
        $items = $this->get();
        return $items[0] ?? null;
    }

    public function count(): int{
        $this->select = ['COUNT(*) AS total'];

        $rows = Database::select(
            $this->toSql(),
            $this->getBindings()
        );
        return (int) $rows[0]['total'];
    }

    public function limit(
        int $limit
    ): static {
        $this->limit = $limit;
        return $this;
    }

    public function get(): array{
        $sql =
            "SELECT "
            . implode(',', $this->select)
            . " FROM {$this->table}";

        $params=[];

        if($this->where){

            $conditions=[];

            foreach($this->where as $where){

                $conditions[]=
                    "{$where[0]} {$where[1]} ?";

                $params[]=
                    $where[2];
            }

            $sql.=
                " WHERE "
                .
                implode(
                    " AND ",
                    $conditions
                );
        }

        if($this->orderBy){
            $sql.=
                " ORDER BY {$this->orderBy}";
        }

        if($this->limit){
            $sql.=
                " LIMIT {$this->limit}";
        }

        $rows = Database::select(
            $sql,
            $params
        );

        return Hydrator::hydrate(
            $this->model,
            $rows
        );
    }

    public function model(string $model): static{
        $this->model = $model;
        return $this;
    }
}