<?php
namespace App\Support;

class Str{
    public static function studly(string $value): string{
        $value = str_replace(['-', '_'], ' ', $value);
        $value = ucwords($value);
        return str_replace(' ', '', $value);
    }

    public static function camel(string $value): string{
        return lcfirst(
            self::studly($value)
        );
    }

    public static function snake(string $value): string{
        return strtolower(
            preg_replace(
                '/(?<!^)[A-Z]/',
                '_$0',
                $value
            )
        );
    }

    public static function slug(string $value): string{
        return strtolower(
            preg_replace(
                '/[^a-zA-Z0-9]+/',
                '-',
                trim($value)
            )
        );
    }

    public static function endsWith(
        string $value,
        string $search
    ): bool {

        return str_ends_with(
            $value,
            $search
        );
    }

    public static function startsWith(
        string $value,
        string $search
    ): bool {
        return str_starts_with(
            $value,
            $search
        );

    }

    public static function plural(string $value): string{
        $value = strtolower($value);

        if (str_ends_with($value, 's')) {
            return $value;
        }

        if (str_ends_with($value, 'z')) {
            return substr($value, 0, -1) . 'ces';
        }

        if (preg_match('/(a|e|i|o|u)$/', $value)) {
            return $value . 's';
        }
        return $value . 'es';
    }   
}