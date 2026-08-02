<?php
namespace App\Console\Commands;
use App\Console\Command;
use Core\FileGenerator;

class MakeControllerCommand extends Command
{
    public function handle(array $arguments): void
    {
        $name = trim($arguments[0] ?? '');

        if ($name === '') {
            echo "Debes indicar el nombre del controlador.\n";
            return;
        }

        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        $path = BASE_PATH . "/app/Controllers/{$name}.php";

        if (file_exists($path)) {
            echo "El controlador ya existe.\n";
            return;
        }

        FileGenerator::create(
            'controller',
            $path,
            [
                'class' => $name
            ]
        );
        echo "✅ Controlador {$name} creado correctamente.\n";
    }
}