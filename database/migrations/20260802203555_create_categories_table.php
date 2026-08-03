<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'categories',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('name', 120)
                    ->unique();

                $table
                    ->string('slug', 150)
                    ->unique();

                $table
                    ->text('description')
                    ->nullable();

                $table
                    ->boolean('active')
                    ->default(1);

                $table->timestamps();
            }
        );
    }

    public function down(): void{
        Schema::dropIfExists('categories');
    }
};