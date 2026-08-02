<?php
namespace Core\Migrations;
use RuntimeException;
use Throwable;
use Core\Database;
use Core\Migration;

class Migrator{
    private string $path;

    public function __construct(?string $path = null){
        $this->path = $path
            ?? BASE_PATH . '/database/migrations';
    }

    public function run(): int{
        $this->ensureRepository();

        $applied = $this->appliedMigrations();
        $files = $this->migrationFiles();
        $batch = $this->nextBatch();

        $executed = 0;

        foreach ($files as $file) {
            $name = pathinfo(
                $file,
                PATHINFO_FILENAME
            );

            if (in_array($name, $applied, true)) {
                continue;
            }

            echo "Migrando: {$name}\n";

            try {
                $migration = $this->loadMigration($file);

                $migration->up();

                $this->recordMigration(
                    $name,
                    $batch
                );

                echo "Migrado:  {$name}\n";

                $executed++;
            } catch (Throwable $exception) {
                echo "Error:    {$name}\n";

                throw $exception;
            }
        }
        return $executed;
    }

    private function ensureRepository(): void{
        Database::query(
            <<<SQL
            CREATE TABLE IF NOT EXISTS `migrations` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `migration` VARCHAR(255) NOT NULL UNIQUE,
                `batch` INT UNSIGNED NOT NULL,
                `ran_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci
            SQL
        );
    }

    private function appliedMigrations(): array{
        $rows = Database::select(
            'SELECT migration FROM migrations ORDER BY id'
        );

        return array_column(
            $rows,
            'migration'
        );
    }

    private function nextBatch(): int{
        $row = Database::first(
            'SELECT COALESCE(MAX(batch), 0) + 1 AS batch
             FROM migrations'
        );

        return (int) ($row['batch'] ?? 1);
    }

    private function migrationFiles(): array{
        if (!is_dir($this->path)) {
            mkdir(
                $this->path,
                0777,
                true
            );
        }

        $files = glob(
            $this->path . '/*.php'
        );

        if ($files === false) {
            return [];
        }

        sort($files, SORT_STRING);

        return $files;
    }

    private function loadMigration(
        string $file
    ): Migration {
        $migration = require $file;

        if (!$migration instanceof Migration) {
            throw new RuntimeException(
                "El archivo {$file} no devuelve una migración válida."
            );
        }

        return $migration;
    }

    private function recordMigration(
        string $name,
        int $batch
    ): void {
        Database::execute(
            'INSERT INTO migrations
                (migration, batch)
             VALUES (?, ?)',
            [
                $name,
                $batch,
            ]
        );
    }
}