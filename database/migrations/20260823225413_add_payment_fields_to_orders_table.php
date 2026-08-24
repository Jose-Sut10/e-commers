<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `orders`

             ADD COLUMN `payment_method_id`
             INT UNSIGNED NULL
             AFTER `shipping_method_id`,

             ADD COLUMN `payment_method_name`
             VARCHAR(100) NULL
             AFTER `shipping_method_name`,

             ADD COLUMN `payment_method_code`
             VARCHAR(50) NULL
             AFTER `payment_method_name`,

             ADD COLUMN `payment_status`
             VARCHAR(20) NOT NULL
             DEFAULT 'pending'
             AFTER `shipping_total`,

             ADD COLUMN `paid_at`
             DATETIME NULL
             AFTER `payment_status`"
        );


        Database::query(
            "ALTER TABLE `orders`

             ADD CONSTRAINT `fk_orders_payment_method`

             FOREIGN KEY (`payment_method_id`)
             REFERENCES `payment_methods` (`id`)

             ON DELETE SET NULL
             ON UPDATE CASCADE"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `orders`
             DROP FOREIGN KEY `fk_orders_payment_method`"
        );


        Database::query(
            "ALTER TABLE `orders`

             DROP COLUMN `payment_method_id`,
             DROP COLUMN `payment_method_name`,
             DROP COLUMN `payment_method_code`,
             DROP COLUMN `payment_status`,
             DROP COLUMN `paid_at`"
        );
    }
};