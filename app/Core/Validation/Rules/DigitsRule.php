<?php
namespace Core\Validation\Rules;
class DigitsRule implements Rule{
    public function __construct(
        protected int $digits
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

        $value = (string) $value;

        if (!ctype_digit($value)) {
            return "El campo {$field} debe contener únicamente números.";
        }

        if (strlen($value) !== $this->digits) {
            return "El campo {$field} debe contener exactamente {$this->digits} dígitos.";
        }

        return null;
    }
}