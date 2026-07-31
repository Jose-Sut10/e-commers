<?php

namespace Core;

class Kernel
{
    public function handle(): void
    {
        require_once __DIR__ . '/../../bootstrap/app.php';

        $router = new Router();

        require_once __DIR__ . '/../../routes/web.php';

        $request = new Request();

        $router->dispatch($request);
    }
}