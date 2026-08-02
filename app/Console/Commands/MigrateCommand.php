<?php
namespace App\Console\Commands;
use Throwable;
use App\Console\Command;
use Core\Migrations\Migrator;

class MigrateCommand extends Command{
    public function handle(array $arguments): void{
        try {
            $executed = (
                new Migrator()
            )->run();

            if ($executed === 0) {
                echo "No hay migraciones pendientes.\n";

                return;
            }

            echo "\n";
            echo "{$executed} migración(es) ejecutada(s).\n";
        } catch (Throwable $exception) {
            fwrite(
                STDERR,
                PHP_EOL
                . "Error de migración: {$exception->getMessage()}"
                . PHP_EOL
                . "Archivo: {$exception->getFile()}"
                . PHP_EOL
                . "Línea: {$exception->getLine()}"
                . PHP_EOL
            );
            exit(1);
        }
    }
}