<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'users',
            function (Blueprint $table): void {
                $table->id();

                $table->string('name', 150);

                $table
                    ->string('email', 150)
                    ->unique();

                $table->string('password', 255);

                $table
                    ->boolean('active')
                    ->default(true);

                $table->timestamps();
            }
        );
    }
    public function down(): void{
        Schema::dropIfExists('users');
    }
};