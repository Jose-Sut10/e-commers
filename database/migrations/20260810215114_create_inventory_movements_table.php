<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'inventory_movements',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('product_id')
                    ->constrained('products')
                    ->onDelete('RESTRICT')
                    ->onUpdate('CASCADE');

                $table
                    ->integer('user_id')
                    ->unsigned()
                    ->nullable();

                $table->string('type', 20);

                $table
                    ->integer('quantity')
                    ->unsigned();

                $table
                    ->integer('previous_stock')
                    ->unsigned();

                $table
                    ->integer('new_stock')
                    ->unsigned();

                $table
                    ->string('reason', 255)
                    ->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void{
        Schema::dropIfExists(
            'inventory_movements'
        );
    }
};