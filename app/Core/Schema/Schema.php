<?php
namespace Core\Schema;
use Core\Database;
use Core\Schema\Grammar\MySqlGrammar;

class Schema{
    public static function create(
        string $table,
        callable $callback
    ): void {
        $blueprint = new Blueprint();
        $callback($blueprint);
        $grammar = new MySqlGrammar();

        $sql = $grammar->compileCreate(
            $table,
            $blueprint
        );
        Database::query($sql);
        echo "Tabla {$table} creada." . PHP_EOL;
    }

    public static function dropIfExists(
        string $table
    ): void {
        Database::query(
            "DROP TABLE IF EXISTS `{$table}`"
        );
    }
}