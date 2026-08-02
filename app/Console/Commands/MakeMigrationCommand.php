<?php

namespace App\Console\Commands;

use App\Console\GeneratorCommand;

class MakeMigrationCommand extends GeneratorCommand
{
    protected string $stub = 'migration';
    protected string $destination = 'database/migrations';
    protected string $suffix = '';
    protected function variables(string $name): array{
        return [];
    }

    public function handle(array $arguments): void{
        $name = trim($arguments[0] ?? '');

        if ($name === '') {
            echo "Debes indicar un nombre.\n";
            return;
        }

        $timestamp = date('YmdHis');
        $this->destination = "database/migrations";
        $path = BASE_PATH . "/{$this->destination}/{$timestamp}_{$name}.php";

        \Core\FileGenerator::create(
            $this->stub,
            $path
        );
        echo "✅ Migración creada correctamente.\n";
    }
}