<?php
namespace Core;
class FileGenerator
{
    public static function create(
        string $stub,
        string $destination,
        array $replacements = []
    ): void {

        $stubPath = BASE_PATH . "/stubs/{$stub}.stub";

        if (!file_exists($stubPath)) {
            die("El stub {$stub} no existe.");
        }

        $content = file_get_contents($stubPath);

        foreach ($replacements as $key => $value) {
            $content = str_replace(
                "{{{$key}}}",
                $value,
                $content
            );
        }
        file_put_contents($destination, $content);
    }
}