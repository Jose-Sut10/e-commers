<?php
namespace Core\Security;
use Core\Session;

class Csrf{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string{
        $token = Session::get(self::SESSION_KEY);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));

            Session::put(
                self::SESSION_KEY,
                $token
            );
        }
        return $token;
    }

    public static function verify(mixed $token): bool{
        if (!is_string($token) || $token === '') {
            return false;
        }

        return hash_equals(
            self::token(),
            $token
        );
    }

    public static function field(): string{
        $token = htmlspecialchars(
            self::token(),
            ENT_QUOTES,
            'UTF-8'
        );

        return sprintf(
            '<input type="hidden" name="_token" value="%s">',
            $token
        );
    }

    public static function regenerate(): void{
        Session::put(
            self::SESSION_KEY,
            bin2hex(random_bytes(32))
        );
    }
}