<?php

namespace Core\ORM;

class Hydrator{
    public static function hydrate(
        string $model,
        array $rows
    ): array {

        $items = [];

        foreach ($rows as $row) {
            $object = new $model();

            foreach ($row as $key => $value) {
                $object->$key = $value;
            }
            $items[] = $object;
        }
        return $items;
    }
}