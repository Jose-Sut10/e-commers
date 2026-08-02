<?php
namespace Core\Validation\Rules;

class EmailRule implements Rule{
    public function validate(
        string $field,
        mixed $value,
        array $data
        ): ?string {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value) || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return "El campo {$field} debe contener un correo electrónico válido.";
        }   
        return null;
    }
}