<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'payment_methods',
            function (Blueprint $table): void {

                $table->id();

                $table ->string('name', 100);
                $table
                    ->string('code', 50)
                    ->unique();

                $table ->string('type', 30);

                $table
                    ->text('instructions')
                    ->nullable();

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
        Schema::dropIfExists('payment_methods');
    }
};