<?php

namespace Core;

class Config
{
    protected static array $config = [];

    public static function set(string $key, mixed $value): void
    {
        self::$config[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$config[$key] ?? $default;
    }

    public static function all(): array
    {
        return self::$config;
    }
}