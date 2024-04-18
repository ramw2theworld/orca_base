<?php

namespace Modules\PaymentProvider\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $currency = DB::table('currencies')->where('code', 'gbp')->first();
        $plans = [
            [
                'amount_trial' => 595,
                'provider_plan_id' => 'price_1Np6S8IZ7GRzcjItIrvlBPGp',
                'name' => 'Basic',
                'plan_code' => 'basic',
                'currency_id' => $currency->id
            ],
            [
                'amount' => 28.80,
                'provider_plan_id' => 'price_1Np87MIZ7GRzcjItzKhlOiXZ',
                'name' => 'Premium',
                'plan_code' => 'premium',
                'currency_id' => $currency->id
            ],
        ];

        DB::table('plans')->insert($plans);
    }
}
