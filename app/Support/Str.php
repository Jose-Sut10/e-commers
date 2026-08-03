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
        $value = self::snake($value);

        /*
        * category → categories
        * company  → companies
        */
        if (
            str_ends_with($value, 'y')
            && !preg_match('/[aeiou]y$/', $value)
        ) {
            return substr($value, 0, -1) . 'ies';
        }

        /*
        * class → classes
        * box   → boxes
        * branch → branches
        */
        if (preg_match('/(s|x|z|ch|sh)$/', $value)) {
            return $value . 'es';
        }

        /*
        * user → users
        * product → products
        */
        return $value . 's';
    }
}