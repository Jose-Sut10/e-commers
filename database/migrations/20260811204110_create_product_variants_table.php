<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'product_variants',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('product_id')
                    ->constrained('products')
                    ->onDelete('CASCADE')
                    ->onUpdate('CASCADE');

                $table->string(
                    'name',
                    150
                );

                $table
                    ->string('sku', 100)
                    ->unique();

                /*
                 * Si es NULL se utiliza
                 * el precio del producto.
                 */
                $table
                    ->decimal('price', 12, 2)
                    ->unsigned()
                    ->nullable();

                $table
                    ->integer('stock')
                    ->unsigned()
                    ->default(0);

                $table
                    ->boolean('active')
                    ->default(1);

                $table->timestamps();
            }
        );
    }

    public function down(): void{
        Schema::dropIfExists(
            'product_variants'
        );
    }
};