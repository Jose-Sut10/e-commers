<?php

namespace Core;

class View
{
    protected static string $layout = 'admin';

    public static function render(
        string $view,
        array $data = [],
        ?string $layout = 'admin'
    ): void {

        extract($data);

        $viewPath = dirname(__DIR__, 2)
            . "/resources/views/{$view}.php";

        if (!file_exists($viewPath)) {
            die("La vista '{$view}' no existe.");
        }

        if ($layout === null) {
            require $viewPath;
            return;
        }

        $layoutPath = dirname(__DIR__, 2)
            . "/resources/views/layouts/{$layout}.php";

        if (!file_exists($layoutPath)) {
            die("El layout '{$layout}' no existe.");
        }

        $content = $viewPath;

        require $layoutPath;
    }
}