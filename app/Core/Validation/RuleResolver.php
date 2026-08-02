<?php
namespace Core\Validation;
use InvalidArgumentException;
use Core\Validation\Rules\Rule;
use Core\Validation\Rules\MinRule;
use Core\Validation\Rules\MaxRule;
use Core\Validation\Rules\EmailRule;
use Core\Validation\Rules\NumericRule;
use Core\Validation\Rules\RequiredRule;

class RuleResolver{
    /**
     * @return Rule[]
     */
    public static function resolve(string|array $rules): array
    {
        $rules = is_array($rules)
            ? $rules
            : explode('|', $rules);

        $resolved = [];

        foreach ($rules as $ruleDefinition) {
            if ($ruleDefinition instanceof Rule) {
                $resolved[] = $ruleDefinition;
                continue;
            }

            if (!is_string($ruleDefinition)) {
                throw new InvalidArgumentException(
                    'La definición de una regla debe ser una cadena o una instancia de Rule.'
                );
            }

            [$name, $parameters] = self::parse($ruleDefinition);
            $resolved[] = self::make($name, $parameters);
        }
        return $resolved;
    }

    private static function parse(string $rule): array
    {
        [$name, $parameterString] = array_pad(
            explode(':', trim($rule), 2),
            2,
            null
        );

        $parameters = $parameterString !== null
            ? array_map('trim', explode(',', $parameterString))
            : [];

        return [
            strtolower(trim($name)),
            $parameters
        ];
    }

    private static function make(
        string $name,
        array $parameters
        ): Rule {
        return match ($name) {
            'required' => new RequiredRule(),
            'email'    => new EmailRule(),
            'numeric'  => new NumericRule(),

            'min' => new MinRule(
                self::numericParameter($name, $parameters)
            ),

            'max' => new MaxRule(
                self::numericParameter($name, $parameters)
            ),

            default => throw new InvalidArgumentException(
                "La regla de validación '{$name}' no existe."
            ),
        };
    }

    private static function numericParameter(
        string $rule,
        array $parameters
        ): int|float {
        $value = $parameters[0] ?? null;

        if ($value === null || !is_numeric($value)) {
            throw new InvalidArgumentException(
                "La regla '{$rule}' requiere un parámetro numérico."
            );
        }

        return str_contains((string) $value, '.')
            ? (float) $value
            : (int) $value;
    }
}