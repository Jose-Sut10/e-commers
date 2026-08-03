<?php
namespace Core\Middleware;
use Core\Auth\Auth;
use Core\Request;
use Core\Session;

class Authenticate implements Middleware{
    /**
     * Rutas que requieren una sesión iniciada.
     */
    protected array $protectedRoutes = [
        '/',
        '/empresa',
        '/usuarios',
        '/categorias',  
    ];

    public function handle(Request $request): void{
        $uri = $request->uri();

        if (!$this->isProtected($uri)) {
            return;
        }

        $user = Auth::user();

        if ($user && (bool) $user->active) {
            return;
        }

        if ($user && !(bool) $user->active) {
            Auth::logout();
        }

        Session::flash(
            'warning',
            'Debes iniciar sesión para acceder al panel.'
        );

        redirect('login');
    }

    private function isProtected(string $uri): bool{
        foreach ($this->protectedRoutes as $route) {
            /*
             * La ruta raíz solo debe coincidir exactamente
             * con "/".
             */
            if ($route === '/' && $uri === '/') {
                return true;
            }

            /*
             * Protege la ruta principal y todas sus subrutas:
             *
             * /empresa
             * /empresa/crear
             * /empresa/editar
             */
            if (
                $route !== '/'
                && (
                    $uri === $route
                    || str_starts_with($uri, $route . '/')
                )
            ) {
                return true;
            }
        }
        return false;
    }
}