<?php

namespace Modules\PaymentProvider\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PaymentProvider\Models\PaymentProvider;
use Modules\PaymentProvider\Models\Plan;

class Subscription extends Model
{
    use HasFactory;
    protected $table = 'subscriptions';
    protected $fillable = [ 
        'refunded_at',
        'customer_id', 
        'name', 
        'original_id',
        'provider_id', 
        'status', 
        'price', 
        'quantity', 
        'plan_id', 
        'trial_ends_at', 
        'ends_at' 
    ];

    protected $guarded = [];
    protected $dates = ['trial_ends_at', 'ends_at'];

    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }
    public function provider()
    {
        return $this->belongsTo(PaymentProvider::class);
    }
    /**
     * Inserts a new Subscription record.
     *
     * @param array $data
     * @return Subscription
     */
    public static function insertSubscription(array $data): Subscription
    {
        return self::create($data);
    }
    public function cancel()
    {
        $this->update([
            'ends_at' => now()->toDateTimeString(),
            'status' => 'canceled'
        ]);
    }
    public function refund()
    {
        $this->update([
            'refunded_at' => now()->toDateTimeString(),
        ]);
    }
}
