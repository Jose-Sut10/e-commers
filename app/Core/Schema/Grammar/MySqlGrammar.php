<?php

namespace Core\Schema\Grammar;
use Core\Schema\Blueprint;
use Core\Schema\ForeignKey;

class MySqlGrammar extends Grammar
{
    public function compileCreate(
        string $table,
        Blueprint $blueprint
    ): string {
        $definitions = [];

        // Columnas
        foreach ($blueprint->getColumns() as $column) {
            $definitions[] = $column->sql();
        }

        // Foreign Keys
        foreach ($blueprint->getForeignKeys() as $foreign) {
            $definitions[] = $this->compileForeign($foreign);
        }

        return sprintf(
            "CREATE TABLE %s (%s)",
            $table,
            implode(", ", $definitions)
        );
    }

    protected function compileForeign(
        ForeignKey $foreign
    ): string {

        $sql = sprintf(
            "FOREIGN KEY (%s) REFERENCES %s(%s)",
            $foreign->column,
            $foreign->table,
            $foreign->reference
        );

        if ($foreign->onDelete) {
            $sql .= " ON DELETE {$foreign->onDelete}";
        }

        if ($foreign->onUpdate) {
            $sql .= " ON UPDATE {$foreign->onUpdate}";
        }
        return $sql;
    }
}