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

    protected function insert(): bool{
        $data = $this->attributes;

        unset($data[$this->primaryKey]);

        $result = Database::insert(
            $this->table,
            $data
        );

        if ($result) {
            $this->attributes[$this->primaryKey] =
                Database::lastInsertId();
            $this->exists = true;
        }
        $this->syncOriginal();
        return $result;
    }

    public function save(): bool{
        if ($this->exists) {
            return $this->update();
        }
        return $this->insert();
    }

    protected function update(): bool{
        $dirty = $this->getDirty();

        if (empty($dirty)) {
            return true;
        }

        unset($dirty[$this->primaryKey]);

        $result = Database::update(
            $this->table,
            $dirty,
            $this->primaryKey,
            $this->attributes[$this->primaryKey]
        );

        if ($result) {
            $this->syncOriginal();
        }
        return $result;
    }

    protected function syncOriginal(): void{
        $this->original = $this->attributes;
    }

    public function isDirty(): bool{
        return $this->attributes !== $this->original;
    }

    public function getDirty(): array{
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (
                !array_key_exists($key, $this->original)
                || $this->original[$key] !== $value
            ) {
                $dirty[$key] = $value;
            }
        }
        return $dirty;
    }

    public static function find(mixed $id): ?static{
        $instance = new static();

        $row = Database::find(
            $instance->table,
            $instance->primaryKey,
            $id
        );

        if (!$row) {
            return null;
        }
        $instance->fill($row);
        $instance->exists = true;
        $instance->syncOriginal();
        return $instance;
    }

        public function delete(): bool{
        if (!$this->exists) {
            return false;
        }

        return Database::delete(
            $this->table,
            $this->primaryKey,
            $this->attributes[$this->primaryKey]
        );
    }

    public static function first(): ?static{
        $instance = new static();

        $row = Database::first(
            sprintf(
                'SELECT * FROM %s ORDER BY %s ASC LIMIT 1',
                $instance->table,
                $instance->primaryKey
            )
        );

        if (!$row) {
            return null;
        }

        $instance->fill($row);
        $instance->exists = true;
        $instance->syncOriginal();
        return $instance;
    }
}