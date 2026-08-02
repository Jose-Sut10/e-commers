<?php
namespace App\Console;
use Core\FileGenerator;

abstract class GeneratorCommand extends Command
{
    protected string $stub;
    protected string $destination;
    protected string $suffix = '';

    public function handle(array $arguments): void
    {
        $name = trim($arguments[0] ?? '');

        if ($name === '') {
            echo "Debes indicar un nombre.\n";
            return;
        }

        if ($this->suffix && !str_ends_with($name, $this->suffix)) {
            $name .= $this->suffix;
        }

        $path = BASE_PATH . '/' . $this->destination . '/' . $name . '.php';

        if (file_exists($path)) {
            echo "El archivo ya existe.\n";
            return;
        }

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        FileGenerator::create(
            $this->stub,
            $path,
            $this->variables($name)
        );

        echo "✅ {$name} creado correctamente.\n";
    }

    protected function variables(string $name): array
    {
        return ['class' => $name];
    }
}