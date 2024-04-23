<?php
//namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Log;
function subscriptionStatus(): array {
    $status = [
        'auth' => false,
        'active' => false,
        'subscription_type' => null,
    ];

    try {
        if (Auth::check()) {
            if(Auth::user()->customer){
                if((Auth::user()->customer->subscriptions) && (Auth::user()->customer->subscriptions !==null)){

                    $nowRow = \Carbon\Carbon::now();
                    $now = $nowRow->format('Y-m-d H:i:s');

                    $hasSubscription = Auth::user()->customer->subscriptions()
                        ->whereIn('status', ['active', 'cancelled', 'expired', 'suspended'])
                        ->first();

                    if ($hasSubscription) {
                        if($hasSubscription->status ==='cancelled' && $hasSubscription->expired_at >= $now){
                            $status['active'] = true;
                        }
                        elseif ($hasSubscription->status ==='cancelled' || $hasSubscription->status ==='expired' || $hasSubscription->status ==='suspended'){
                            $status['active'] = false;
                        }
                        else{
                            $status['active'] = true;
                        }
                        $status['auth'] = true;
                        $status['subscription_type'] = 'active';
                    }
                    else {
                        return $status;
//                        Log::error("User has no active subscriptions");
//                        throw new Exception("User has no active subscriptions");
                    }
                }
                else {
                    Log::error("User has no associated customer");
                    throw new Exception("User has no associated customer");
                }
            }
        }else{
            Log::error("User is not authenticated");
            throw new Exception("User is not authenticated");
        }
    } catch (Exception $e) {
        Log::error('Something went wrong while checking user subscription ' . $e->getMessage());
        throw new $e($e->getMessage());
    }
    return $status;
}
