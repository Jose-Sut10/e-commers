<?php
namespace App\Models;
use Core\Model;
use Core\Database;

class StoreSetting extends Model{
    protected string $table = 'store_settings';

    /*CONFIGURACIÓN ACTUAL*/

    public static function current(): static{
        $instance = new static();

        $row =
            Database::first(
                "SELECT *
                 FROM `store_settings`
                 ORDER BY `id` ASC
                 LIMIT 1"
            );

        if ($row) {
            return $instance->newFromDatabase($row);
        }

        /*
         * Si todavía no existe configuración,
         * devolvemos un modelo nuevo con
         * valores predeterminados.
         */

        return new static([
            'business_name' => null,
            'phone' => null,
            'whatsapp' => null,
            'email' => null,
            'address' => null,
            'business_hours' => null,
            'logo_path' => null,
            'currency_code' => 'GTQ',
            'currency_symbol' => 'Q',
            'facebook_url' => null,
            'instagram_url' => null,
            'tiktok_url' => null,
            'bank_name' => null,
            'bank_account_name' => null,
            'bank_account_number' => null,
            'bank_account_type' => null,
        ]);
    }
}