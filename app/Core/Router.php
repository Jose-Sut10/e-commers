<?php

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void{
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, array $action): void{
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch(Request $request): void{
    $method = $request->method();
    $uri = $request->uri();

    if (!isset($this->routes[$method][$uri])) {
        http_response_code(404);
        echo "<h1>404</h1>";
        echo "<p>No existe la ruta: {$uri}</p>";
        return;
    }

    [$controller, $action] = $this->routes[$method][$uri];
    $controller = new $controller();
    $controller->$action();
    }
}