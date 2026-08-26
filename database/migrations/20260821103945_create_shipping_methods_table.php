<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'shipping_methods',
            function (Blueprint $table): void {

                $table->id();
                $table->string('name',100);

                $table
                    ->text('description')
                    ->nullable();

                $table
                    ->decimal(
                        'price',
                        12,
                        2
                    )
                    ->unsigned()
                    ->default(0);

                $table
                    ->integer('sort_order')
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
            'shipping_methods'
        );
    }
};