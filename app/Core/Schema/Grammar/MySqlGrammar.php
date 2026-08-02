<?php

namespace Core\Schema\Grammar;

use Core\Schema\Blueprint;

class MySqlGrammar extends Grammar{
    public function compileCreate(
        string $table,
        Blueprint $blueprint
    ): string
    {
        $columns = [];
        foreach ($blueprint->getColumns() as $column) {
            $columns[] = $column->sql();
        }

        return sprintf(
            "CREATE TABLE %s (%s)",
            $table,
            implode(", ", $columns)
        );
    }
}