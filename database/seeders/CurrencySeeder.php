<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency = array(
            array('id' => 1, 'country_id' => 1, 'currency_code' => 'AFN', 'currency_name' => 'Afghan Afghani'),
            array('id' => 2, 'country_id' => 2, 'currency_code' => 'ALL', 'currency_name' => 'Albanian lek'),
            array('id' => 3, 'country_id' => 3, 'currency_code' => 'DZD', 'currency_name' => 'Algerian dinar'),
            array('id' => 4, 'country_id' => 4, 'currency_code' => 'USD', 'currency_name' => 'US dollar'),
            array('id' => 5, 'country_id' => 5, 'currency_code' => 'EUR', 'currency_name' => 'Euro'),
            array('id' => 6, 'country_id' => 6, 'currency_code' => 'AOA', 'currency_name' => 'Angolan kwanza'),
            array('id' => 7, 'country_id' => 7, 'currency_code' => 'XCD', 'currency_name' => 'East Caribbean dollar'),

            array('id' => 9, 'country_id' => 9, 'currency_code' => 'XCD', 'currency_name' => 'East Caribbean dollar'),
            array('id' => 10, 'country_id' => 10, 'currency_code' => 'ARS', 'currency_name' => 'Argentine peso'),
            array('id' => 11, 'country_id' => 11, 'currency_code' => 'AMD', 'currency_name' => 'Armenian dram'),
            array('id' => 12, 'country_id' => 12, 'currency_code' => 'AWG', 'currency_name' => 'Aruban guilder'),
            array('id' => 13, 'country_id' => 13, 'currency_code' => 'AUD', 'currency_name' => 'Australian dollar'),
            array('id' => 14, 'country_id' => 14, 'currency_code' => 'EUR', 'currency_name' => 'Euro'),
            array('id' => 15, 'country_id' => 15, 'currency_code' => 'AZN', 'currency_name' => 'New azerbaijani Manat'),
            array('id' => 16, 'country_id' => 16, 'currency_code' => 'BSD', 'currency_name' => 'Bahamian dollar'),


            array('id' => 229, 'country_id' => 229, 'currency_code' => 'AED', 'currency_name' => 'UAE dirham'),
            array('id' => 231, 'country_id' => 231, 'currency_code' => 'USD', 'currency_name' => 'US dollar'),
        );
    }
}
