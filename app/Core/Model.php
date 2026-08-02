<?php
namespace Core;

abstract class Model{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;

    /**
     * Cambiar a false en modelos cuyas tablas
     * no tengan created_at y updated_at.
     */
    protected bool $timestamps = true;
    protected string $createdAtColumn = 'created_at';
    protected string $updatedAtColumn = 'updated_at';
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
        return $this->exists
            ? $this->update()
            : $this->insert();
    }

    protected function insert(): bool{
        if ($this->timestamps) {
            $now = $this->freshTimestamp();

            $this->attributes[$this->createdAtColumn] ??= $now;
            $this->attributes[$this->updatedAtColumn] ??= $now;
        }

        $data = $this->attributes;
        unset($data[$this->primaryKey]);

        $result = Database::insert(
            $this->table,
            $data
        );

        if (!$result) {
            return false;
        }

        $this->attributes[$this->primaryKey] =
            Database::lastInsertId();

        $this->exists = true;
        $this->syncOriginal();
        return true;
    }

    protected function update(): bool{
        /*
         * Primero comprobamos si el usuario realmente
         * modificó algún atributo.
         */
        $dirty = $this->getDirty();

        unset($dirty[$this->primaryKey]);

        if (empty($dirty)) {
            return true;
        }

        if ($this->timestamps) {
            $this->attributes[$this->updatedAtColumn] =
                $this->freshTimestamp();

            $dirty = $this->getDirty();

            unset($dirty[$this->primaryKey]);
        }

        $id = $this->attributes[$this->primaryKey] ?? null;

        if ($id === null) {
            return false;
        }

        $result = Database::update(
            $this->table,
            $dirty,
            $this->primaryKey,
            $id
        );

        if ($result) {
            $this->syncOriginal();
        }

        return $result;
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
        return $instance->newFromDatabase($row);
    }

    public static function first(): ?static{
        $instance = new static();

        $sql = sprintf(
            'SELECT * FROM %s ORDER BY %s ASC LIMIT 1',
            $instance->table,
            $instance->primaryKey
        );

        $row = Database::first($sql);

        if (!$row) {
            return null;
        }
        return $instance->newFromDatabase($row);
    }

    public function delete(): bool{
        if (!$this->exists) {
            return false;
        }

        $id = $this->attributes[$this->primaryKey] ?? null;

        if ($id === null) {
            return false;
        }

        $deleted = Database::delete(
            $this->table,
            $this->primaryKey,
            $id
        );

        if ($deleted) {
            $this->exists = false;
        }
        return $deleted;
    }

    public function isDirty(): bool{
        return !empty($this->getDirty());
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

    protected function syncOriginal(): void{
        $this->original = $this->attributes;
    }

    protected function freshTimestamp(): string{
        return date('Y-m-d H:i:s');
    }

    protected function newFromDatabase(array $attributes): static{
        $model = new static();
        $model->fill($attributes);
        $model->exists = true;
        $model->syncOriginal();
        return $model;
    }
}