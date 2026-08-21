<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `products`

             ADD COLUMN `sale_price`
             DECIMAL(12,2) UNSIGNED NULL
             AFTER `price`,

             ADD COLUMN `sale_starts_at`
             DATETIME NULL
             AFTER `sale_price`,

             ADD COLUMN `sale_ends_at`
             DATETIME NULL
             AFTER `sale_starts_at`"
        );

        Database::query(
            "ALTER TABLE `product_variants`

             ADD COLUMN `sale_price`
             DECIMAL(12,2) UNSIGNED NULL
             AFTER `price`,

             ADD COLUMN `sale_starts_at`
             DATETIME NULL
             AFTER `sale_price`,

             ADD COLUMN `sale_ends_at`
             DATETIME NULL
             AFTER `sale_starts_at`"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `product_variants`

             DROP COLUMN `sale_ends_at`,
             DROP COLUMN `sale_starts_at`,
             DROP COLUMN `sale_price`"
        );

        Database::query(
            "ALTER TABLE `products`

             DROP COLUMN `sale_ends_at`,
             DROP COLUMN `sale_starts_at`,
             DROP COLUMN `sale_price`"
        );
    }
};