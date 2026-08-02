<?php
namespace App\Controllers;
use Core\Controller;
use Core\Request;
use Core\Session;
use Core\Auth\Auth;

class AuthController extends Controller{
    public function showLogin(): void{
        if (Auth::check()) {
            redirect('');
        }

        view('auth/login', [
            'title' => 'Iniciar sesión',
        ]);
    }

    public function login(): void{
        if (Auth::check()) {
            redirect('');
        }

        $request = new Request();
        $input = $request->all();

        $result = validator($input, [
            'email'    => 'required|email|max:150',
            'password' => 'required',
        ])->validate();

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash('old', [
                'email' => $input['email'] ?? '',
            ]);
            redirect('login');
        }

        $authenticated = Auth::attempt(
            (string) ($input['email'] ?? ''),
            (string) ($input['password'] ?? '')
        );

        if (!$authenticated) {
            Session::flash('errors', [
                'general' => [
                    'El correo o la contraseña son incorrectos.',
                ],
            ]);

            Session::flash('old', [
                'email' => $input['email'] ?? '',
            ]);
            redirect('login');
        }

        Session::flash(
            'success',
            'Has iniciado sesión correctamente.'
        );
        redirect('');
    }

    public function logout(): void{
        Auth::logout();

        Session::flash(
            'success',
            'La sesión se cerró correctamente.'
        );
        redirect('login');
    }
}