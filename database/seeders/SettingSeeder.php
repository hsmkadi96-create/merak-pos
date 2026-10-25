<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert some stuff
        DB::table('settings')->insert(
            [
                'id' => 1,
                'email' => 'admin@example.com',
                'currency_id' => 1,
                'client_id' => 1,
                'sms_gateway' => 1,
                'point_to_amount_rate' => 1,
                'is_invoice_footer' => 0,
                'invoice_footer' => null,
                'warehouse_id' => null,
                'CompanyName' => 'MERAK POS',
                'CompanyPhone' => '6315996770',
                'CompanyAdress' => '3618 Abia Martin Drive',
                'footer' => 'MERAK POS - Gestion Stock & Caisse',
                'developed_by' => 'H-KADI',
                'logo' => 'logo-default.png',
                'app_name' => 'MERAK POS | Gestion Stock & Caisse',
                'page_title_suffix' => 'Gestion Stock & Caisse',
                'favicon' => 'favicon.ico',
                'default_language' => 'en',
                'quotation_with_stock' => 1,
                'show_language' => 1,
                'default_tax' => 0,
                'payment_cib_enabled' => 0,
                'payment_dahabia_enabled' => 0,
                'payment_chifaa_enabled' => 0,
                'zakat_nisab_threshold' => 50000,
                'restaurant_enabled' => 0,
                'clinic_enabled' => 0,
                'face_id_enabled' => 0,
            ]

        );
    }
}
