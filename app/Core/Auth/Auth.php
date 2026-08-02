<?php
namespace Core\Auth;
use Core\Session;
use App\Models\User;

class Auth{
    private const SESSION_KEY = 'auth_user_id';

    public static function attempt(
        string $email,
        string $password
    ): bool {
        $user = User::findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!(bool) $user->active) {
            return false;
        }

        if (!password_verify(
            $password,
            (string) $user->password
        )) {
            return false;
        }

        session_regenerate_id(true);

        Session::put(
            self::SESSION_KEY,
            (int) $user->id
        );
        return true;
    }

    public static function check(): bool{
        return Session::has(self::SESSION_KEY);
    }

    public static function id(): ?int{
        $id = Session::get(self::SESSION_KEY);

        return $id !== null
            ? (int) $id
            : null;
    }

    public static function user(): ?User{
        $id = self::id();

        if ($id === null) {
            return null;
        }

        $user = User::find($id);

        if (!$user) {
            self::logout();
            return null;
        }
        return $user;
    }

    public static function logout(): void{
        Session::forget(self::SESSION_KEY);
        session_regenerate_id(true);
    }
}