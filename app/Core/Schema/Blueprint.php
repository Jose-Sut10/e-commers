<?php
namespace Core\Schema;

class Blueprint{
    protected array $columns=[];

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
    ): Column {
        $column = new Column(
            $name,
            "VARCHAR($length)"
    );
    $this->columns[] = $column;
    return $column;
}

    public function integer(string $name): Column{
        $column = new Column($name, 'INT');
        $this->columns[] = $column;
        return $column;
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