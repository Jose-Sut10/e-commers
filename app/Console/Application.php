<?php

namespace App\Console;

use App\Console\Commands\MigrateCommand;
use App\Console\Commands\MakeModelCommand;
use App\Console\Commands\MakeControllerCommand;
use App\Console\Commands\MakeMigrationCommand;

class Application
{
    protected array $commands = [
        'make:controller' => MakeControllerCommand::class,
        'make:model' => MakeModelCommand::class,
        'make:migration' => MakeMigrationCommand::class,
        'migrate' => MigrateCommand::class,
    ];

    public function run(array $argv): void{
        $commandName = $argv[1] ?? 'about';

        if ($commandName === 'about') {
            $this->about();
            return;
        }

        if (!isset($this->commands[$commandName])) {
            echo "El comando '{$commandName}' no existe.\n";
            return;
        }

        $commandClass = $this->commands[$commandName];
        $command = new $commandClass();
        $command->handle(
            array_slice($argv, 2)
        );
    }

    private function about(): void{
        echo PHP_EOL;
        echo "==============================\n";
        echo "   EcommerceCMS Framework\n";
        echo "==============================\n";
        echo "Versión: 1.0.0\n";
        echo "PHP: " . PHP_VERSION . "\n\n";
    }
}