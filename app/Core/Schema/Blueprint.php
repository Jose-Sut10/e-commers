<?php
namespace Core\Schema;

class Blueprint{
    protected array $columns=[];
    protected array $foreignKeys = [];

    protected function addColumn(
        string $name,
        string $type
        ): Column{
        $column = new Column($name, $type);
        $this->columns[] = $column;
        return $column;
    }

    public function foreignId(
        string $column
    ): ForeignKey
    {
        $this->integer($column)
            ->unsigned();

        $foreign = new ForeignKey($column);
        $this->foreignKeys[] = $foreign;
        return $foreign;
    }

    public function getForeignKeys(): array{
        return $this->foreignKeys;
    }

    public function foreignIdFor(string $model): ForeignKey{
        $class = class_basename($model);
        $column = strtolower($class) . '_id';
        return $this->foreignId($column);
    }

    public function id(): Column{
        return $this->addColumn(
            'id',
            'INT'
        )
        ->unsigned()
        ->autoIncrement()
        ->primary();
    }

    public function string(
        string $name,
        int $length = 255
        ): Column {
        return $this->addColumn(
            $name,
            "VARCHAR($length)"
        );
    }

    public function integer(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'INT'
        );
    }

    public function boolean(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'BOOLEAN'
        );
    }

    public function decimal(
        string $name,
        int $precision,
        int $scale
        ): Column {
        return $this->addColumn(
            $name,
            "DECIMAL($precision,$scale)"
        );
    }

    public function timestamps(): void{
        $this->addColumn(
            'created_at',
            'TIMESTAMP'
        );
        $this->addColumn(
            'updated_at',
            'TIMESTAMP'
        );
    }

    public function getColumns(): array{
        return $this->columns;
    }

    public function text(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'TEXT'
        );
    }

    public function longText(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'LONGTEXT'
        );
    }

    public function date(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'DATE'
        );
    }

    public function dateTime(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'DATETIME'
        );
    }

    public function time(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'TIME'
        );
    }

    public function double(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'DOUBLE'
        );
    }

    public function json(
        string $name
        ): Column {
        return $this->addColumn(
            $name,
            'JSON'
        );
    }
}