<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPaymentMethod extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'payment_method_type',
        'card_type',
        'bin',
        'last4',
        'token'
    ];

    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class, 'customer_id', 'id');
    // }
    /**
     * Inserts a new payment method record.
     *
     * @param array $data
     * @return CustomerPaymentMethod
     */
    public static function insertPaymentMethod(array $data): CustomerPaymentMethod
    {
        return self::create($data);
    }
}
