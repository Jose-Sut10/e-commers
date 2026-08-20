<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `orders`
             ADD COLUMN `customer_id`
             INT UNSIGNED NULL
             AFTER `id`"
        );

        Database::query(
            "ALTER TABLE `orders`
             ADD CONSTRAINT `fk_orders_customer`
             FOREIGN KEY (`customer_id`)
             REFERENCES `customers` (`id`)
             ON DELETE SET NULL
             ON UPDATE CASCADE"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `orders`
             DROP FOREIGN KEY `fk_orders_customer`"
        );
        Database::query(
            "ALTER TABLE `orders`
             DROP COLUMN `customer_id`"
        );
    }
};