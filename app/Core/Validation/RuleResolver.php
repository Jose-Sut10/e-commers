<?php
namespace Core\Validation;
use Core\Validation\Rules\RequiredRule;

class RuleResolver{
    public static function resolve(string $rules): array
    {
        $resolved = [];
        foreach (explode('|', $rules) as $rule) {
            if ($rule === 'required') {
                $resolved[] = new RequiredRule();
            }
        }
        return $resolved;
    }
}