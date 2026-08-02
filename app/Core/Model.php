<?php

namespace Core;
use Core\ORM\Builder;

abstract class Model{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;

    public function __construct(array $attributes = []){
        $this->fill($attributes);
    }

    public function fill(array $attributes): static{
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    public function __get(string $key): mixed{
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void{
        $this->attributes[$key] = $value;
    }

    public function toArray(): array{
        return $this->attributes;
    }

    public function save(): bool{
        if ($this->exists) {
            echo "UPDATE";
        } else {
            echo "INSERT";
        }
    }
}