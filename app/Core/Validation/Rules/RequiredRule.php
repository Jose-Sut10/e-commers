<?php
namespace Core\Validation\Rules;

class RequiredRule implements Rule{
    public function validate(
        string $field,
        mixed $value,
        array $data
    ): ?string {
        if ($value === null || $value === '') {
            return "El campo {$field} es obligatorio.";
        }
        return null;
    }
}