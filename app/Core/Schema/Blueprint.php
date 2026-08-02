<?php
namespace Core\Schema;

class Blueprint{
    protected array $columns=[];
        private function addColumn(
        string $name,
        string $type
        ): Column{
        $column = new Column($name, $type);
        $this->columns[] = $column;
        return $column;
    }

    public function id(): Column{
        $column = new Column(
            'id',
            'INT'
        );
        $column
            ->unsigned()
            ->autoIncrement()
            ->primary();

        $this->columns[] = $column;
        return $column;
    }

    public function string(
        string $name,
        int $length = 255
        ): Column{
        return $this->addColumn(
            $name,
            "VARCHAR($length)"
        );
    }

    public function integer(string $name): Column{
        return $this->addColumn(
            $name,
            'INT'
        );
    }

    public function boolean(string $name): Column{
        $column = new Column(
            $name,
            'BOOLEAN'
        );
        $this->columns[] = $column;
        return $column;
    }

    public function decimal(
        string $name,
        int $precision,
        int $scale
        ): Column {

        $column = new Column(
            $name,
            "DECIMAL($precision,$scale)"
        );
        $this->columns[] = $column;
        return $column;
    }

    public function timestamps(): void{
        $this->columns[]=
            new Column(
                'created_at',
                'TIMESTAMP'
            );

        $this->columns[]=
            new Column(
                'updated_at',
                'TIMESTAMP'
            );
    }

    public function getColumns(): array{
        return $this->columns;
    }
}