<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `orders`

             ADD COLUMN `coupon_id`
             INT UNSIGNED NULL
             AFTER `customer_id`,

             ADD COLUMN `coupon_code`
             VARCHAR(50) NULL
             AFTER `notes`,

             ADD COLUMN `discount_total`
             DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0
             AFTER `subtotal`"
        );

        Database::query(
            "ALTER TABLE `orders`

             ADD CONSTRAINT `fk_orders_coupon`

             FOREIGN KEY (`coupon_id`)
             REFERENCES `coupons` (`id`)

             ON DELETE SET NULL
             ON UPDATE CASCADE"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `orders`
             DROP FOREIGN KEY `fk_orders_coupon`"
        );

        Database::query(
            "ALTER TABLE `orders`

             DROP COLUMN `coupon_id`,
             DROP COLUMN `coupon_code`,
             DROP COLUMN `discount_total`"
        );
    }
};