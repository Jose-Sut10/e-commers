<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name',150);
            $table->string('trade_name',150)->nullable();
            $table->string('tax_id',50)->nullable();
            $table->string('email',150)->nullable();
            $table->string('phone',30)->nullable();
            $table->string('whatsapp',30)->nullable();
            $table->string('website',150)->nullable();
            $table->string('logo')->nullable();
            $table->string('currency',10)->default('GTQ');
            $table->string('timezone',100)
                  ->default('America/Guatemala');
            $table->decimal('tax',5,2)
                  ->default(12);
            $table->boolean('active')
                  ->default(true);
            $table->timestamps();

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};