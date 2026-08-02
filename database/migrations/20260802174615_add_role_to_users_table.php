<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `users`
             ADD COLUMN `role` VARCHAR(20)
             NOT NULL DEFAULT 'user'
             AFTER `password`"
        );

        /*
         * Como actualmente el primer usuario es el administrador,
         * lo promovemos automáticamente.
         */
        Database::query(
            "UPDATE `users`
             SET `role` = 'admin'
             ORDER BY `id` ASC
             LIMIT 1"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `users`
             DROP COLUMN `role`"
        );
    }
};