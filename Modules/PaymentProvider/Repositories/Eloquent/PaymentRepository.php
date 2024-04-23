<?php

namespace Modules\PaymentProvider\Repositories\Eloquent;

use Illuminate\Http\Request;
use Modules\PaymentProvider\Repositories\Contracts\PaymentRepositoryInterface;
use Modules\PaymentProvider\Services\StripeService;

class PaymentRepository implements PaymentRepositoryInterface
{
    private $stripeService;
    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }
    public function createPaymentIntent(array $data){
        $paymentIntent = $this->stripeService->createPaymentIntent($data);
        return $paymentIntent;
    }

    public function createSubscription(array $data){
        $paymentIntent = $this->stripeService->createPaymentIntent($data);
        return $paymentIntent;
    }
}
