<?php

namespace Core;
use Core\Session;

class Kernel{
    public function handle(): void{
        require_once __DIR__ . '/../../bootstrap/app.php';
        Session::start();

        $router = new Router();

        require_once __DIR__ . '/../../routes/web.php';

        $request = new Request();

        $router->dispatch($request);
    }
}