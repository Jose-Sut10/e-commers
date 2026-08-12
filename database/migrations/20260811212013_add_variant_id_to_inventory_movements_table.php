<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `inventory_movements`
             ADD COLUMN `variant_id`
             INT UNSIGNED NULL
             AFTER `product_id`"
        );

        Database::query(
            "ALTER TABLE `inventory_movements`
             ADD CONSTRAINT
             `fk_inventory_movements_variant`

             FOREIGN KEY (`variant_id`)
             REFERENCES `product_variants` (`id`)

             ON DELETE SET NULL
             ON UPDATE CASCADE"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `inventory_movements`
             DROP FOREIGN KEY
             `fk_inventory_movements_variant`"
        );

        Database::query(
            "ALTER TABLE `inventory_movements`
             DROP COLUMN `variant_id`"
        );
    }
};