<?php
namespace Core\Validation;

class Validator{
    protected array $data;
    protected array $rules;
    protected ValidationResult $result;

    public function __construct(
        array $data,
        array $rules
        ) {
        $this->data = $data;
        $this->rules = $rules;
        $this->result = new ValidationResult();
    }

    public static function make(
        array $data,
        array $rules
        ): static {
        return new static($data, $rules);
    }

    public function validate(): ValidationResult{
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;

            foreach (RuleResolver::resolve($rules) as $rule) {
                $error = $rule->validate(
                    $field,
                    $value,
                    $this->data
                );

                if ($error !== null) {
                    $this->result->add($field, $error);
                }
            }
        }
        return $this->result;
    }
}