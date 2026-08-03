<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\User;
use Core\Auth\Auth;

class UserController extends Controller{
    public function edit(): void{
        $id = filter_var(
            $_GET['id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$id) {
            Session::flash(
                'warning',
                'El usuario indicado no es válido.'
            );

            redirect('usuarios');
        }

        $user = User::find($id);

        if (!$user) {
            Session::flash(
                'warning',
                'El usuario no fue encontrado.'
            );

            redirect('usuarios');
        }

        view('users/edit', [
            'title' => 'Editar usuario',
            'user'  => $user,
        ]);
    }
    
    public function update(): void
    {
        $request = new Request();
        $input = $request->all();

        $id = filter_var(
            $input['id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$id) {
            Session::flash(
                'warning',
                'El usuario indicado no es válido.'
            );

            redirect('usuarios');
        }

        $user = User::find($id);

        if (!$user) {
            Session::flash(
                'warning',
                'El usuario no fue encontrado.'
            );

            redirect('usuarios');
        }

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

        $active = isset($input['active'])
            ? 1
            : 0;

        $result = validator($input, [
            'name' => 'required|min:3|max:150',
            'email' => 'required|email|max:150',
            'password' => 'max:255',
            'role' => 'required',
        ])->validate();

        /*
        * La contraseña solo se valida cuando el administrador
        * escribe una nueva.
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
        * El correo puede pertenecer al usuario que estamos editando,
        * pero no a otra cuenta.
        */
        if ($result->first('email') === null) {
            $existingUser = User::findByEmail($email);

            if (
                $existingUser
                && (int) $existingUser->id !== (int) $user->id
            ) {
                $result->add(
                    'email',
                    'Ya existe otro usuario registrado con este correo.'
                );
            }
        }

        /*
        * El administrador conectado no puede quitarse su propio
        * permiso ni desactivar su propia cuenta.
        */
        if (Auth::id() === (int) $user->id) {
            if ($role !== 'admin') {
                $result->add(
                    'role',
                    'No puedes quitar el rol de administrador a tu propia cuenta.'
                );
            }

            if ($active !== 1) {
                $result->add(
                    'active',
                    'No puedes desactivar tu propia cuenta.'
                );
            }
        }

        /*
        * No permitimos eliminar o desactivar al último
        * administrador activo.
        */
        $removesActiveAdmin =
            $user->isAdmin()
            && (bool) $user->active
            && (
                $role !== 'admin'
                || $active !== 1
            );

        if (
            $removesActiveAdmin
            && User::countActiveAdmins() <= 1
        ) {
            $result->add(
                'role',
                'Debe permanecer al menos un administrador activo.'
            );
        }

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash('old', [
                'name' => $input['name'] ?? '',
                'email' => $input['email'] ?? '',
                'role' => $role,
                'active' => (string) $active,
            ]);

            redirect(
                'usuarios/editar?id=' . (int) $user->id
            );
        }

        try {
            $user->name = trim(
                (string) ($input['name'] ?? '')
            );

            $user->email = $email;
            $user->role = $role;
            $user->active = $active;

            /*
            * La contraseña actual se conserva cuando el campo
            * queda vacío.
            */
            if ($password !== '') {
                $user->password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );
            }

            if (!$user->save()) {
                throw new RuntimeException(
                    'El modelo no pudo actualizar el usuario.'
                );
            }

            Session::flash(
                'success',
                'El usuario fue actualizado correctamente.'
            );

            redirect('usuarios');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash('errors', [
                'general' => [
                    'No fue posible actualizar el usuario.',
                ],
            ]);

            Session::flash('old', [
                'name' => $input['name'] ?? '',
                'email' => $input['email'] ?? '',
                'role' => $role,
                'active' => (string) $active,
            ]);

            redirect(
                'usuarios/editar?id=' . (int) $user->id
            );
        }
    }

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