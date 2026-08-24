<?php
use Core\Migration;
use Core\Database;

return new class extends Migration{
    public function up(): void{
        Database::query(
            "ALTER TABLE `orders`

             ADD COLUMN `payment_proof_path`
             VARCHAR(255) NULL
             AFTER `payment_method_code`,

             ADD COLUMN `payment_proof_uploaded_at`
             DATETIME NULL
             AFTER `payment_proof_path`"
        );
    }

    public function down(): void{
        Database::query(
            "ALTER TABLE `orders`

             DROP COLUMN `payment_proof_uploaded_at`,
             DROP COLUMN `payment_proof_path`"
        );
    }
};