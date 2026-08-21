<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'coupons',
            function (Blueprint $table): void {

                $table->id();
                $table
                    ->string('code', 50)
                    ->unique();

                /*
                 * percentage = porcentaje
                 * fixed      = monto fijo
                 */
                $table->string(
                    'type',
                    20
                );

                $table
                    ->decimal(
                        'value',
                        12,
                        2
                    )
                    ->unsigned();

                $table
                    ->decimal(
                        'min_order',
                        12,
                        2
                    )
                    ->unsigned()
                    ->default(0);

                $table
                    ->integer(
                        'usage_limit'
                    )
                    ->unsigned()
                    ->nullable();

                $table
                    ->integer(
                        'used_count'
                    )
                    ->unsigned()
                    ->default(0);

                $table
                    ->dateTime(
                        'starts_at'
                    )
                    ->nullable();

                $table
                    ->dateTime(
                        'ends_at'
                    )
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
            'coupons'
        );
    }
};