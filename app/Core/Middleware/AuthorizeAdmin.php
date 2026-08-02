<?php
namespace Core\Middleware;
use Core\Auth\Auth;
use Core\Request;

class AuthorizeAdmin implements Middleware{
    protected array $protectedRoutes = [
        '/usuarios',
    ];

    public function handle(Request $request): void{
        if (!$this->isProtected($request->uri())) {
            return;
        }

        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return;
        }

        http_response_code(403);

        view('errors/403', [
            'title' => 'Acceso denegado',
        ]);

        exit;
    }

    private function isProtected(string $uri): bool{
        foreach ($this->protectedRoutes as $route) {
            if (
                $uri === $route
                || str_starts_with($uri, $route . '/')
            ) {
                return true;
            }
        }
        return false;
    }
}