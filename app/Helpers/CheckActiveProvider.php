<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;

class CheckActiveProvider {
    public static function checkActiveProvider() {
        try {
            return DB::table('payment_providers')->where('is_active', true)->first();
        }
        catch (Exception $e) {
            return null;
        }

    }
}
