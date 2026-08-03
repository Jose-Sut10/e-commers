<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\User;

class UserController extends Controller{
    public function index(): void
    {
        $users = User::all();

        view('users/index', [
            'title' => 'Usuarios',
            'users' => $users,
        ]);
    }

    public function create(): void{
        view('users/create', [
            'title' => 'Registrar usuario',
        ]);
    }

    public function store(): void{
        $request = new Request();
        $input = $request->all();

        $email = mb_strtolower(
            trim((string) ($input['email'] ?? ''))
        );

        $password = (string) (
            $input['password'] ?? ''
        );

        $confirmation = (string) (
            $input['password_confirmation'] ?? ''
        );

        $role = (string) (
            $input['role'] ?? 'user'
        );

        $result = validator($input, [
            'name' => 'required|min:3|max:150',
            'email' => 'required|email|max:150',
            'password' => 'required|max:255',
            'password_confirmation' => 'required',
            'role' => 'required',
        ])->validate();

        /*
         * Validación específica de contraseña.
         */
        if (
            $password !== ''
            && mb_strlen($password) < 8
        ) {
            $result->add(
                'password',
                'La contraseña debe contener al menos 8 caracteres.'
            );
        }

        if (
            $password !== ''
            && $password !== $confirmation
        ) {
            $result->add(
                'password_confirmation',
                'La confirmación de la contraseña no coincide.'
            );
        }

        /*
         * Solo aceptamos estos roles.
         */
        if (!in_array(
            $role,
            ['admin', 'user'],
            true
        )) {
            $result->add(
                'role',
                'El rol seleccionado no es válido.'
            );
        }

        /*
         * Comprobar que el correo no esté registrado.
         */
        if (
            $result->first('email') === null
            && User::findByEmail($email)
        ) {
            $result->add(
                'email',
                'Ya existe un usuario registrado con este correo.'
            );
        }

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            /*
             * Nunca guardamos contraseñas en flash.
             */
            Session::flash('old', [
                'name' => $input['name'] ?? '',
                'email' => $input['email'] ?? '',
                'role' => $role,
                'active' => isset($input['active'])
                    ? '1'
                    : '0',
            ]);

            redirect('usuarios/crear');
        }

        try {
            $user = new User([
                'name' => trim(
                    (string) ($input['name'] ?? '')
                ),

                'email' => $email,

                'password' => password_hash(
                    $password,
                    PASSWORD_DEFAULT
                ),

                'role' => $role,

                'active' => isset($input['active'])
                    ? 1
                    : 0,
            ]);

            if (!$user->save()) {
                throw new RuntimeException(
                    'El modelo no pudo guardar el usuario.'
                );
            }

            Session::flash(
                'success',
                'El usuario fue registrado correctamente.'
            );

            redirect('usuarios');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash('errors', [
                'general' => [
                    'No fue posible registrar el usuario.',
                ],
            ]);

            Session::flash('old', [
                'name' => $input['name'] ?? '',
                'email' => $input['email'] ?? '',
                'role' => $role,
                'active' => isset($input['active'])
                    ? '1'
                    : '0',
            ]);
            redirect('usuarios/crear');
        }
    }
}