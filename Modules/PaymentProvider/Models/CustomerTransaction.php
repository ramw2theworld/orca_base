<?php

namespace Modules\PaymentProvider\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PaymentProvider\Models\Currency;
use Modules\PaymentProvider\Models\PaymentProvider;

class CustomerTransaction extends Model
{
    use HasFactory;
    protected $table = 'customer_transactions';

    protected $fillable = [
        'customer_id',
        'subscription_id',
        'currency_id',
        'payment_provider_id',
        'payment_status',
        'amount',
        'transaction_id',
        'payment_date'
    ];
    /**
     * Inserts a new Payment record.
     *
     * @param array $data
     * @return CustomerTransaction
     */
    public static function insertPayment(array $data): CustomerTransaction
    {
        return self::create($data);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function paymentProvider()
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id', 'id');
    }
}
