<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PaymentProvider\Models\Currency;

class Language extends Model {
    use HasFactory;
    protected $table = 'languages';

    protected $fillable = [
        'code',
        'name',
        'is_active',
        'currency_id',
    ];
    
    protected $guarded = [];

    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public static function langCurrency() {
        return optional(Language::where('code', app()->getLocale())->first())->currency;
    }

}
