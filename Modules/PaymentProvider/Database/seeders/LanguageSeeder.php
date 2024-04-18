<?php

namespace Modules\PaymentProvider\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        $langauages = [
            ['code' => 'en', 'name'=>'English', 'is_active' => false, 'currency_id'=> 1],
            ['code' => 'en-gb', 'name'=>'English United Kingdom','is_active' => true, 'currency_id'=> 3],
        ];
        DB::table('languages')->insert($langauages);
    }
}
