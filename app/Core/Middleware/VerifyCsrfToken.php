<?php
namespace Core\Middleware;
use Core\Request;
use Core\Security\Csrf;

class VerifyCsrfToken implements Middleware{
    /**
     * Métodos que no necesitan protección CSRF.
     */
    private const SAFE_METHODS = [
        'GET',
        'HEAD',
        'OPTIONS',
    ];

    /**
     * Rutas que pueden excluirse de la validación.
     *
     * Ejemplo:
     * protected array $except = ['/webhook'];
     */
    protected array $except = [];

    public function handle(Request $request): void{
        if (
            in_array(
                $request->method(),
                self::SAFE_METHODS,
                true
            )
        ) {
            return;
        }

        if ($this->isExcepted($request->uri())) {
            return;
        }

        $token = $request->input('_token');

        if (!Csrf::verify($token)) {
            $this->reject();
        }
    }

    private function isExcepted(string $uri): bool{
        return in_array(
            $uri,
            $this->except,
            true
        );
    }

    private function reject(): never{
        http_response_code(419);

        view('errors/419', [
            'title' => 'Sesión expirada',
        ]);
        exit;
    }
}