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
        $keys = explode('.', $key);

        $config = self::$config;

        foreach ($keys as $segment) {

            if (!isset($config[$segment])) {
                return $default;
            }

            $config = $config[$segment];
        }

        return $config;
    }

    public static function all(): array
    {
        return self::$config;
    }
}