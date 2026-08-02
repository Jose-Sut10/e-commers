<?php
namespace Core\Validation\Rules;

class MaxRule implements Rule{
    public function __construct(
        protected int|float $maximum
    ) {
    }

    public function validate(
        string $field,
        mixed $value,
        array $data
        ): ?string {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value > $this->maximum
                ? "El campo {$field} no debe ser mayor que {$this->maximum}."
                : null;
        }

        if (is_string($value)) {
            return mb_strlen($value) > $this->maximum
                ? "El campo {$field} no debe superar {$this->maximum} caracteres."
                : null;
        }

        if (is_array($value)) {
            return count($value) > $this->maximum
                ? "El campo {$field} no debe contener más de {$this->maximum} elementos."
                : null;
        }
        return "El campo {$field} tiene un formato no válido.";
    }
}