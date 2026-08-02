<?php
namespace Core;

class Session{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::ageFlashData();
    }

    public static function put(string $key, mixed $value): void{
        $_SESSION[$key] = $value;
    }

    public static function get(
        string $key,
        mixed $default = null
        ): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool{
        return array_key_exists($key, $_SESSION);
    }

    public static function forget(string $key): void{
        unset($_SESSION[$key]);
    }

    public static function flush(): void{
        $_SESSION = [];
    }

    public static function flash(
        string $key,
        mixed $value
        ): void {
        $_SESSION['_flash']['new'][$key] = true;
        $_SESSION[$key] = $value;
    }

    private static function ageFlashData(): void{
        $old = array_keys(
            $_SESSION['_flash']['old'] ?? []
        );

        foreach ($old as $key) {
            unset($_SESSION[$key]);
        }

        $_SESSION['_flash']['old'] =
            $_SESSION['_flash']['new'] ?? [];

        $_SESSION['_flash']['new'] = [];
    }
}