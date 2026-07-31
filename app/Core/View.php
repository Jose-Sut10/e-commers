<?php

namespace Core;

class View
{
    public static function render(string $view, array $data = [])
    {
        extract($data);

        $path = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($path)) {
            die("La vista {$view} no existe.");
        }

        require $path;
    }
}