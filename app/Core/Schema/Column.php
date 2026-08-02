<?php

namespace Core\Schema;

class Column
{
    public string $name;
    public string $type;
    public array $attributes = [];

    public function __construct(string $name, string $type){
        $this->name = $name;
        $this->type = $type;
    }

    public function nullable(): static{
        $this->attributes[] = 'NULL';
        return $this;
    }

    public function unique(): static{
        $this->attributes[] = 'UNIQUE';
        return $this;
    }

    public function default(mixed $value): static{
        if (is_string($value)) {
            $value = "'{$value}'";
        }

        $this->attributes[] = "DEFAULT {$value}";
        return $this;
    }

    public function unsigned(): static{
        $this->attributes[] = 'UNSIGNED';
        return $this;
    }

    public function autoIncrement(): static{
        $this->attributes[] = 'AUTO_INCREMENT';
        return $this;
    }

    public function primary(): static{
        $this->attributes[] = 'PRIMARY KEY';
        return $this;
    }

    public function sql(): string{
        return trim(
            "{$this->name} {$this->type} "
            . implode(' ', $this->attributes)
        );
    }
}