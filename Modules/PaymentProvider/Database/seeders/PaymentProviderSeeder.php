<?php

namespace Modules\PaymentProvider\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentProviderSeeder extends Seeder
{
    public function run()
    {
        $providers = [
            ['name' => 'Stripe', 'provider_code' => 'stripe', 'is_active' => 1],
            ['name' => 'PayPal', 'provider_code' => 'paypal', 'is_active' => 1],
            ['name' => 'HiPay', 'provider_code' => 'hipay', 'is_active' => 1],
            ['name' => 'Braintree', 'provider_code' => 'braintree', 'is_active' => 1],
        ];

        DB::table('payment_providers')->insert($providers);
    }
}
