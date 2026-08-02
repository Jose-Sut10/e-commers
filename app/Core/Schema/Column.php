<?php

namespace Core\Schema;

class Column{
    public string $name;
    public string $type;
    public array $options=[];
    public function __construct(
        string $name,
        string $type,
        array $options=[]
    ){
        $this->name=$name;
        $this->type=$type;
        $this->options=$options;

    }
}