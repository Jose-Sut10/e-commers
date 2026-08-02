<?php
namespace Core\Validation;

class Validator{
    protected array $data;
    protected array $rules;
    protected ValidationResult $result;

    public function __construct(
        array $data,
        array $rules
        ){
        $this->data = $data;
        $this->rules = $rules;
        $this->result = new ValidationResult();
    }

    public static function make(
        array $data,
        array $rules
        ): static{

        return new static(
            $data,
            $rules
        );
    }
    public function validate(): ValidationResult{
        return $this->result;
    }
}