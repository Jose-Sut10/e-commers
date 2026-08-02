<?php
namespace Core\ORM;
use Core\Database;
use Core\ORM\Hydrator;

class Builder
{
    protected string $model;
    protected string $table;
    protected array $select = ['*'];
    protected array $where = [];
    protected ?string $orderBy = null;
    protected ?int $limit = null;

    public function table(string $table): static{
        $this->table = $table;
        return $this;
    }

    public function where(
        string $column,
        mixed $value
    ): static {
        $this->where[] = [
            $column,
            '=',
            $value
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