<?php
use Core\Migration;
use Core\Schema\Schema;
use Core\Schema\Blueprint;

return new class extends Migration{
    public function up(): void{
        Schema::create(
            'store_settings',
            function (Blueprint $table): void {

                $table->id();


                /*INFORMACIÓN GENERAL*/

                $table
                    ->string(
                        'business_name',
                        150
                    )
                    ->nullable();

                $table
                    ->string(
                        'phone',
                        30
                    )
                    ->nullable();

                $table
                    ->string(
                        'whatsapp',
                        30
                    )
                    ->nullable();

                $table
                    ->string(
                        'email',
                        150
                    )
                    ->nullable();

                $table
                    ->text(
                        'address'
                    )
                    ->nullable();

                $table
                    ->text(
                        'business_hours'
                    )
                    ->nullable();

                /*IDENTIDAD*/

                $table
                    ->string(
                        'logo_path',
                        255
                    )
                    ->nullable();

                /*MONEDA*/

                $table
                    ->string(
                        'currency_code',
                        10
                    )
                    ->default('GTQ');

                $table
                    ->string(
                        'currency_symbol',
                        10
                    )
                    ->default('Q');


                /*REDES SOCIALES*/

                $table
                    ->string(
                        'facebook_url',
                        255
                    )
                    ->nullable();

                $table
                    ->string(
                        'instagram_url',
                        255
                    )
                    ->nullable();

                $table
                    ->string(
                        'tiktok_url',
                        255
                    )
                    ->nullable();

                /*DATOS BANCARIOS*/

                $table
                    ->string(
                        'bank_name',
                        150
                    )
                    ->nullable();

                $table
                    ->string(
                        'bank_account_name',
                        150
                    )
                    ->nullable();

                $table
                    ->string(
                        'bank_account_number',
                        100
                    )
                    ->nullable();

                $table
                    ->string(
                        'bank_account_type',
                        100
                    )
                    ->nullable();


                $table->timestamps();
            }
        );
    }

    public function down(): void{
        Schema::dropIfExists(
            'store_settings'
        );
    }
};