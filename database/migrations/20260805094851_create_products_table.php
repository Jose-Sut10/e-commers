<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'products',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('category_id')
                    ->constrained('categories')
                    ->onDelete('RESTRICT')
                    ->onUpdate('CASCADE');

                $table
                    ->string('name', 150);

                $table
                    ->string('slug', 180)
                    ->unique();

                $table
                    ->string('sku', 80)
                    ->unique();

                $table
                    ->text('description')
                    ->nullable();

                $table
                    ->decimal('price', 12, 2)
                    ->unsigned()
                    ->default(0);

                $table
                    ->decimal('cost', 12, 2)
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
        Schema::dropIfExists('products');
    }
};