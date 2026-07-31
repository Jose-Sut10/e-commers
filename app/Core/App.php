<?php

namespace Core;

class App
{
    public static function run()
    {
        Config::set('app', require __DIR__ . '/../../config/app.php');

        $router = new Router();

        require __DIR__ . '/../../routes/web.php';

        $request = new Request();

        $router->dispatch($request);
    }
}