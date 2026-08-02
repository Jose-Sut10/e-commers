<?php

use Core\Config;
use Core\View;
use Core\Component;

if (!function_exists('config')) {

    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }

}

if (!function_exists('view')) {

    function view(
        string $view,
        array $data = [],
        ?string $layout = 'admin'
    ): void {

        View::render(
            $view,
            $data,
            $layout
        );

    }

}

if (!function_exists('dd')) {

    function dd(mixed $value): never
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        die();
    }

}

if (!function_exists('dump')) {

    function dump(mixed $value): void
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }

}

if (!function_exists('url')) {

    function url(string $path = ''): string
    {
        return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
    }

}

if (!function_exists('component')) {
    function component(string $component, array $data = []): void
    {
        Component::render(
            $component,
            $data
        );
    }
}