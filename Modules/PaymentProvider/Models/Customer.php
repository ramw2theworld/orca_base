<?php

namespace Modules\PaymentProvider\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function customerTransactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }
    public function paymentMethods()
    {
        return $this->hasMany(CustomerPaymentMethod::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
