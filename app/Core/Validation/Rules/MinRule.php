<?php
namespace Core\Validation\Rules;

class MinRule implements Rule{
    public function __construct(
        protected int|float $minimum
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
            return (float) $value < $this->minimum
                ? "El campo {$field} debe ser como mínimo {$this->minimum}."
                : null;
        }

        if (is_string($value)) {
            return mb_strlen($value) < $this->minimum
                ? "El campo {$field} debe contener al menos {$this->minimum} caracteres."
                : null;
        }

        if (is_array($value)) {
            return count($value) < $this->minimum
                ? "El campo {$field} debe contener al menos {$this->minimum} elementos."
                : null;
        }
        return "El campo {$field} tiene un formato no válido.";
    }
}