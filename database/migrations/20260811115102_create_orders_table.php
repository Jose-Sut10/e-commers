<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'orders',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('number', 40)
                    ->unique();

                $table->string(
                    'customer_name',
                    150
                );

                $table->string(
                    'customer_phone',
                    30
                );

                $table
                    ->string('customer_email', 150)
                    ->nullable();

                $table->text(
                    'customer_address'
                );

                $table
                    ->text('notes')
                    ->nullable();

                $table
                    ->decimal('subtotal', 12, 2)
                    ->unsigned();

                $table
                    ->decimal('total', 12, 2)
                    ->unsigned();

                $table
                    ->string('status', 30)
                    ->default('pending');

                $table->timestamps();
            }
        );
    }

    public function down(): void{
        Schema::dropIfExists('orders');
    }
};