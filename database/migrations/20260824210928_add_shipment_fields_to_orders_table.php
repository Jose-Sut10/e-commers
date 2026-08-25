<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `orders`

             ADD COLUMN `shipping_carrier`
             VARCHAR(100) NULL
             AFTER `shipping_method_name`,

             ADD COLUMN `tracking_number`
             VARCHAR(150) NULL
             AFTER `shipping_carrier`,

             ADD COLUMN `shipping_guide_path`
             VARCHAR(255) NULL
             AFTER `tracking_number`,

             ADD COLUMN `shipped_at`
             DATETIME NULL
             AFTER `shipping_guide_path`,

             ADD COLUMN `delivered_at`
             DATETIME NULL
             AFTER `shipped_at`"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `orders`

             DROP COLUMN `delivered_at`,
             DROP COLUMN `shipped_at`,
             DROP COLUMN `shipping_guide_path`,
             DROP COLUMN `tracking_number`,
             DROP COLUMN `shipping_carrier`"
        );
    }
};