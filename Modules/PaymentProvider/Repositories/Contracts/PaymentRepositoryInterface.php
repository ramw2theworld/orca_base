<?php

namespace Modules\PaymentProvider\Repositories\Contracts;

use Illuminate\Http\Request;

interface PaymentRepositoryInterface
{
    public function createPaymentIntent(array $data);
    public function createSubscription(array $data);

}
