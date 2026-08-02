<?php
namespace Core\Schema;

class ForeignKey{
    public string $column;
    public string $table;
    public string $reference = 'id';
    public ?string $onDelete = null;
    public ?string $onUpdate = null;

    public function __construct(
        string $column
    ){
        $this->column = $column;
    }

    public function constrained(
        string $table,
        string $reference = 'id'
    ): static{
        $this->table = $table;
        $this->reference = $reference;
        return $this;
    }

    public function onDelete(
        string $action
    ): static{
        $this->onDelete = strtoupper($action);
        return $this;
    }

    public function onUpdate(
        string $action
    ): static{
        $this->onUpdate = strtoupper($action);
        return $this;
    }
}