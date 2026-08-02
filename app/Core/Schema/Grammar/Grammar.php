<?php
namespace Core\Schema\Grammar;
use Core\Schema\Blueprint;

abstract class Grammar{
    abstract public function compileCreate(
        string $table,
        Blueprint $blueprint
    ): string;
}