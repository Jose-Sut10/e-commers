<?php

namespace Core;

use PDO;
use PDOException;

class Database{
    private static ?PDO $connection = null;

    public static function connect(): PDO{
        if (self::$connection === null) {

            $config = Config::get('database');

            if (!is_array($config)) {
                throw new \RuntimeException(
                    'La configuración de la base de datos no fue cargada.'
                );
            }

            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=utf8mb4",
                $config['host'],
                $config['database']
            );

            try {

                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password']
                );

                self::$connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );
                self::$connection->exec(
                    "SET time_zone = '-06:00'"
                );

            } catch (PDOException $e) {

                die("Error de conexión: " . $e->getMessage());

            }

        }

        return self::$connection;
    }

    public static function query(string $sql): void{
    self::connect()->exec($sql);
    }

    public static function select(string $sql, array $params = []): array{
    $stmt = self::connect()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
    }

    public static function first(string $sql, array $params = []): array|null{
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function execute(string $sql, array $params = []): bool{
        $stmt = self::connect()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function lastInsertId(): string{
        return self::connect()->lastInsertId();
    }

    public static function insert(
        string $table,
        array $data
        ): bool
        {
            $columns = array_keys($data);

            $placeholders = implode(
                ',',
                array_fill(0, count($columns), '?')
            );

            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $table,
                implode(',', $columns),
                $placeholders
            );

            return self::execute(
                $sql,
                array_values($data)
            );
    }

    public static function update(
        string $table,
        array $data,
        string $primaryKey,
        mixed $id
        ): bool{
        $columns = [];

        foreach ($data as $column => $value) {
            $columns[] = "{$column} = ?";
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $table,
            implode(', ', $columns),
            $primaryKey
        );

        $params = array_values($data);
        $params[] = $id;
        return self::execute($sql, $params);
    }

    public static function find(
        string $table,
        string $primaryKey,
        mixed $id
        ): ?array{
        $sql = sprintf(
            "SELECT * FROM %s WHERE %s = ? LIMIT 1",
            $table,
            $primaryKey
        );
        return self::first($sql, [$id]);
    }

    public static function delete(
        string $table,
        string $primaryKey,
        mixed $id
        ): bool{
        $sql = sprintf(
            "DELETE FROM %s WHERE %s = ?",
            $table,
            $primaryKey
        );
        return self::execute($sql, [$id]);
    }
}