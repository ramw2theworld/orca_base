<?php

namespace Modules\PaymentProvider\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Plan extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'plan_code',
        'amount_trial',
        'amount_premium',
        'provider_plan_id',
        'payment_provider_id',
        'currency_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // Define your hidden attributes here
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Define your casts here
    ];

    public function paymentProvider(){
        return $this->belongsTo(PaymentProvider::class);
    }
}
