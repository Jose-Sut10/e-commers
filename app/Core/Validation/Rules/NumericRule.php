<?php
namespace Core\Validation\Rules;

class NumericRule implements Rule{
    public function validate(
        string $field,
        mixed $value,
        array $data
        ): ?string {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return "El campo {$field} debe ser numérico.";
        }
        return null;
    }
}