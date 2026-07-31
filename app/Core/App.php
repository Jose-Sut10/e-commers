<?php

namespace Core;

class App
{
    public static function run(): void
    {
        Config::set(
            'app',
            require __DIR__ . '/../../config/app.php'
        );

        Config::set(
            'database',
            require __DIR__ . '/../../config/database.php'
        );

        $router = new Router();

        require_once __DIR__ . '/../../routes/web.php';

        $request = new Request();

        $router->dispatch($request);
    }
}