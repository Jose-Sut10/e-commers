<?php
namespace Core\Schema;
use Core\Database;

class Schema{
    public static function create(
        string $table,
        callable $callback
    ): void{

        $blueprint=new Blueprint();
        $callback($blueprint);
        $columns=[];

        foreach(
            $blueprint->getColumns()
            as $column
        ){
            $columns[]=
                "{$column->name} {$column->type}";
        }

        $sql=
            "CREATE TABLE {$table} ("
            .
            implode(',',$columns)
            .
            ")";
        Database::query($sql);
        echo "Tabla {$table} creada.\n";
    }
}