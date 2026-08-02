<?php

namespace Core;

class Request
{
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function uri(): string{
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $basePath = Config::get('app.base_path', '');

    // Eliminar el base_path
    if ($basePath && str_starts_with($uri, $basePath)) {
        $uri = substr($uri, strlen($basePath));
    }

    // Eliminar index.php si viene en la URL
    $uri = preg_replace('#^/index\.php#', '', $uri);

    // Si queda vacío, devolver "/"
    return $uri ?: '/';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }
}