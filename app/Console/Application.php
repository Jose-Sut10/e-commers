<?php

namespace App\Console;

use App\Console\Commands\MakeControllerCommand;
use App\Console\Commands\MakeModelCommand;

class Application{
    public function run(array $argv): void
    {
        $command = $argv[1] ?? '';

        switch ($command) {

            case 'about':
                $this->about();
                break;

            case 'make:controller':
                (new MakeControllerCommand())->handle(array_slice($argv, 2));
                break;
            case 'make:model':
                (new MakeModelCommand())->handle(array_slice($argv, 2));
            break;
            default:
                echo "Comando no encontrado.\n";
        }
    }

    private function about(): void
    {
        echo PHP_EOL;
        echo "==============================\n";
        echo "   EcommerceCMS Framework\n";
        echo "==============================\n";
        echo "Versión: 1.0.0\n";
        echo "PHP: " . PHP_VERSION . "\n\n";
    }
}