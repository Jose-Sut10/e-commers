<?php

namespace Core;

class Component
{
    public static function render(string $component, array $data = []): void
    {
        extract($data);
        $path = BASE_PATH . "/resources/views/components/{$component}.php";
        if (!file_exists($path)) {
            die("El componente '{$component}' no existe.");
        }
        require $path;
    }
}