<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'order_items',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('order_id')
                    ->constrained('orders')
                    ->onDelete('CASCADE')
                    ->onUpdate('CASCADE');

                $table
                    ->foreignId('product_id')
                    ->constrained('products')
                    ->onDelete('RESTRICT')
                    ->onUpdate('CASCADE');

                /*
                 * Guardamos copias históricas.
                 */
                $table->string(
                    'product_name',
                    150
                );

                $table->string(
                    'sku',
                    80
                );

                $table
                    ->decimal('unit_price', 12, 2)
                    ->unsigned();

                $table
                    ->integer('quantity')
                    ->unsigned();

                $table
                    ->decimal('subtotal', 12, 2)
                    ->unsigned();

                $table->timestamps();
            }
        );
    }

    public function down(): void{
        Schema::dropIfExists(
            'order_items'
        );
    }
};