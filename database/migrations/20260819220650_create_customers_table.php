<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'customers',
            function (Blueprint $table): void {
                $table->id();
                $table->string(
                    'name',
                    150
                );
                $table
                    ->string(
                        'phone',
                        30
                    )
                    ->unique();
                $table
                    ->string(
                        'email',
                        150
                    )
                    ->nullable();
                $table
                    ->text('address')
                    ->nullable();
                $table
                    ->boolean('active')
                    ->default(1);
                $table->timestamps();
            }
        );
    }

    public function down(): void{
        Schema::dropIfExists(
            'customers'
        );
    }
};