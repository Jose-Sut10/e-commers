<?php
namespace Core\Schema;

class Blueprint{
    protected array $columns=[];

    public function id(): void{
        $this->columns[] = new Column(
            'id',
            'INT AUTO_INCREMENT PRIMARY KEY'
        );
    }

    public function string(
        string $name,
        int $length=255
    ): void{
        $this->columns[]=
            new Column(
                $name,
                "VARCHAR($length)"
            );
    }

    public function integer(string $name): void{
        $this->columns[]=
            new Column(
                $name,
                "INT"
            );
    }

    public function boolean(string $name): void{
        $this->columns[]=
            new Column(
                $name,
                "BOOLEAN"
            );
    }

    public function decimal(
        string $name,
        int $precision,
        int $scale
    ): void{

        $this->columns[]=
            new Column(
                $name,
                "DECIMAL($precision,$scale)"
            );

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