<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `orders`

             ADD COLUMN `shipping_method_id`
             INT UNSIGNED NULL
             AFTER `coupon_id`,

             ADD COLUMN `shipping_method_name`
             VARCHAR(100) NULL
             AFTER `coupon_code`,

             ADD COLUMN `shipping_total`
             DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0
             AFTER `discount_total`"
        );

        Database::query(
            "ALTER TABLE `orders`

             ADD CONSTRAINT `fk_orders_shipping_method`

             FOREIGN KEY (`shipping_method_id`)
             REFERENCES `shipping_methods` (`id`)

             ON DELETE SET NULL
             ON UPDATE CASCADE"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `orders`
             DROP FOREIGN KEY `fk_orders_shipping_method`"
        );

        Database::query(
            "ALTER TABLE `orders`

             DROP COLUMN `shipping_method_id`,
             DROP COLUMN `shipping_method_name`,
             DROP COLUMN `shipping_total`"
        );
    }
};