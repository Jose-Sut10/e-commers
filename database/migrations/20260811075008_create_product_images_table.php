<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'product_images',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('product_id')
                    ->constrained('products')
                    ->onDelete('CASCADE')
                    ->onUpdate('CASCADE');

                $table->string('path', 255);

                $table->string('original_name', 255)
                    ->nullable();

                $table->string('mime_type', 100)
                    ->nullable();

                $table->integer('size')
                    ->unsigned()
                    ->default(0);

                $table->boolean('is_primary')
                    ->default(0);

                $table->timestamps();
            }
        );
    }

    public function down(): void{
        Schema::dropIfExists(
            'product_images'
        );
    }
};