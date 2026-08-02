<?php

namespace App\Console\Commands;

use App\Console\Command;

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

        if (!$name) {
            echo "Debes indicar el nombre del controlador.\n";
            return;
        }

        $path = BASE_PATH . "/app/Controllers/{$name}.php";

        if (file_exists($path)) {
            echo "El controlador ya existe.\n";
            return;
        }

        $content = <<<PHP
<?php
namespace App\Controllers;
use Core\Controller;

class {$name} extends Controller
{

}
PHP;

        file_put_contents($path, $content);
        echo "✅ Controlador {$name} creado correctamente.\n";
    }
}