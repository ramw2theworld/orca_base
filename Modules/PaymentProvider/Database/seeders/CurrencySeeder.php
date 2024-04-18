<?php

namespace Modules\PaymentProvider\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $currencies = [
            ['code' => 'usd', 'symbol' => '$'],
            ['code' => 'eur', 'symbol' => '€'],
            ['code' => 'gbp', 'symbol' => '£'],
            ['code' => 'jyp', 'symbol' => '¥'],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
