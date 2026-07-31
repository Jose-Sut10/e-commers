<?php

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
{
    echo "Registrando ruta: {$uri}<br>";

    $this->routes['GET'][$uri] = $action;
}

    public function post(string $uri, array $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch(Request $request)
    {

        $method = $request->method();
        $uri = $request->uri();

        var_dump($method);
        var_dump($uri);
        die();

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            die("Ruta no encontrada.");
        }

        [$controller, $action] = $this->routes[$method][$uri];

        $controller = new $controller();

        return $controller->$action();
    }
}