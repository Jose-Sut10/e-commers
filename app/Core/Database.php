<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO{
        if (self::$connection === null) {

            $config = Config::get('database');

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
}