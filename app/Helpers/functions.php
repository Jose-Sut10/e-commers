<?php

use Core\Config;
use Core\View;
use Core\Component;
use Core\Validation\Validator; 
use Core\Session;

if (!function_exists('session')) {
    function session(
        ?string $key = null,
        mixed $default = null
    ): mixed {
        if ($key === null) {
            return Session::class;
        }
        return Session::get($key, $default);
    }
}

if (!function_exists('old')) {
    function old(
        string $key,
        mixed $default = ''
        ): mixed {
        $old = Session::get('old', []);

        return $old[$key] ?? $default;
    }
}

if (!function_exists('errors')) {
    function errors(): array{
        return Session::get('errors', []);
    }
}

if (!function_exists('error')) {
    function error(string $field): ?string{
        return errors()[$field][0] ?? null;
    }
}

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
    function url(string $path = ''): string{
        $baseUrl = rtrim(
            config('app.url', ''),
            '/'
        );
        $path = ltrim($path, '/');
        return $path === ''
            ? $baseUrl
            : "{$baseUrl}/{$path}";
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

function validator(
    array $data,
    array $rules
    ): Validator {

    return Validator::make(
        $data,
        $rules
    );
}

if (!function_exists('redirect')) {
    function redirect(string $path): never{
        header('Location: ' . url($path));
        exit;
    }
}