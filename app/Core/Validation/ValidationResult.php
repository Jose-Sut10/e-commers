<?php
namespace Core\Validation;

class ValidationResult{
    protected array $errors = [];

    public function add(
        string $field,
        string $message
        ): void{
        $this->errors[$field][] = $message;
    }

    public function fails(): bool{
        return !empty($this->errors);
    }

    public function passes(): bool{
        return empty($this->errors);
    }

    public function errors(): array{
        return $this->errors;
    }

    public function first(
        string $field
    ): ?string{
        return $this->errors[$field][0] ?? null;
    }
}