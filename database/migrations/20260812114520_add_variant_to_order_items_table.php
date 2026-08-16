<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `order_items`
             ADD COLUMN `variant_id`
             INT UNSIGNED NULL
             AFTER `product_id`"
        );

        Database::query(
            "ALTER TABLE `order_items`
             ADD COLUMN `variant_name`
             VARCHAR(150) NULL
             AFTER `product_name`"
        );

        Database::query(
            "ALTER TABLE `order_items`
             ADD CONSTRAINT `fk_order_items_variant`
             FOREIGN KEY (`variant_id`)
             REFERENCES `product_variants` (`id`)
             ON DELETE SET NULL
             ON UPDATE CASCADE"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `order_items`
             DROP FOREIGN KEY `fk_order_items_variant`"
        );

        Database::query(
            "ALTER TABLE `order_items`
             DROP COLUMN `variant_id`"
        );

        Database::query(
            "ALTER TABLE `order_items`
             DROP COLUMN `variant_name`"
        );
    }
};