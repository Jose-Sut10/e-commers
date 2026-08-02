<?php
namespace App\Console\Commands;
use App\Console\GeneratorCommand;

class MakeModelCommand extends GeneratorCommand
{
    protected string $stub = 'model';
    protected string $destination = 'app/Models';
    protected function variables(string $name): array
    {
        return [
            'class' => $name,
            'table' => strtolower($name) . 's'
        ];
    }
}