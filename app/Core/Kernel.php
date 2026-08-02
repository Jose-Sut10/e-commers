<?php
namespace Core;
use Core\Middleware\Authenticate;
use Core\Middleware\AuthorizeAdmin;
use Core\Middleware\VerifyCsrfToken;

class Kernel{
    public function handle(): void{
        require_once BASE_PATH . '/bootstrap/app.php';
        Session::start();
        $router = new Router();
        require_once BASE_PATH . '/routes/web.php';
        $request = new Request();

        $middleware = [
            new VerifyCsrfToken(),
            new Authenticate(),
            new AuthorizeAdmin(),
        ];

        foreach ($middleware as $item) {
            $item->handle($request);
        }
        $router->dispatch($request);
    }
}