<?php
namespace Modules\PaymentProvider\Services;

use App\Jobs\SendOrderMailJob;
use Modules\PaymentProvider\Models\Currency;
use App\Models\Base\CustomerPaymentMethod;
use App\Models\Base\CustomerTransaction;
use App\Models\Base\PaymentProvider;
use Modules\PaymentProvider\Models\Plan;
use App\Models\Base\Subscription as SubscriptionModel;
use App\Services\SendEmail;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Modules\PaymentProvider\Models\Language;
use Modules\PaymentProvider\Models\Plan as ModelsPlan;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Subscription;

class StripeService {
    private $stripeSecret;
    public function __construct()
    {
        $this->stripeSecret = config('paymentprovider.payment.stripe.secret_key');
    }
    
    public function createPaymentIntent($data)
    {
        try {
            Stripe::setApiKey($this->stripeSecret);
            $stripeCustomer = Customer::create([
                'email' => $data['email'],               
                'name' => $data['billing_details']['name'],
            ]);

            $currency = Language::langCurrency();
            $plan = $currency->plans->where('plan_code', '48h basic subscription')->first();
            $trial_amt = $plan->amount_trial;

            $paymentIntent = PaymentIntent::create([
                'amount' => $trial_amt,
                'currency' => $plan->currency->code,
                'payment_method' => $data['payment_method_id'],
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'customer' => $stripeCustomer->id,
                'setup_future_usage' => 'off_session',
                'return_url' => env("APP_URL").'/payment-redirect',
            ]);
            return [
                'paymentStatus' => $paymentIntent->status,
                'clientSecret' => $paymentIntent->client_secret,
                'customerId' => $stripeCustomer->id
            ];

        } catch (\Exception $e) {
            Log::warning(get_called_class() . ': ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function createSubscription($data)
    {
        return $data;
        try {
            Stripe::setApiKey($this->stripeSecret);

            $stripePaymentMethodId = $data['payment_method_id'];
            $stripeCustomerId = $data['customer_id'];

            $authUser = $data['request_user'];
            dd($data); 
            // TJ081368A
            $stripePaymentMethod = PaymentMethod::retrieve($stripePaymentMethodId);
            $stripePaymentMethod->attach(['customer' => $stripeCustomerId]);

            $currency = Language::langCurrency();
            $plan = $currency->plans->where('plan_code', 'premium')->first();

            if($authUser->customer){
                $authUser->customer->update([
                    'provider_customer_id' => $stripeCustomerId,
                ]);
            }

            $authUser = Auth::user();
            if (!$authUser) {
                throw new \Exception("User or customer data not found.");
            }
            $customerData = $authUser->customer ?? null;

            DB::beginTransaction();
            try{
                if($customerData === null){
                    $customerData = $this->createNewCustomer('Just a test customer');
                }
            }catch(Exception $exception){
                Log::error(get_called_class() . ': ' . $exception->getMessage());
                throw new $exception("An error occurred while creating customer: ". $exception->getMessage());
            }

            // Create a subscription
            $subscription = Subscription::create([
                'customer' => $stripeCustomerId,
                'items' => [
                    [
                        'price' => $plan->provider_plan_id,
                    ],
                ],
                'trial_period_days' => 2,
                'default_payment_method' => $stripePaymentMethodId,
            ]);
            $creditCardDetails = $stripePaymentMethod->card;
            $authUser->assignRole('Premium');

            $paymentProviderId = PaymentProvider::where('provider_code', 'stripe')->first()->id;
            try{
                $newPaymentMethod = CustomerPaymentMethod::insertPaymentMethod([
                    'customer_id' => $customerData->id,
                    'payment_method_type' => 'stripe',
                    'card_type' => $creditCardDetails->brand,
                    'bin' => $creditCardDetails->fingerprint,
                    'last4' => $creditCardDetails->last4
                ]);
            }catch(Exception $exception){
                Log::error("CustomerPyMethod: ".get_called_class() . ': ' . $exception->getMessage());
                throw new $exception("An error occurred while creating Customer Payment Method: ". $exception->getMessage());
            }
            try{
                $expiry_at = $subscription->current_period_end?\Carbon\Carbon::createFromTimestamp($subscription->current_period_end)
                    ->format('Y-m-d H:i:s'):\Carbon\Carbon::createFromTimestamp($subscription->trial_end)
                    ->format('Y-m-d H:i:s');

                $newSubscription = SubscriptionModel::insertSubscription([
                    'customer_id' => $customerData->id,
                    'provider_id' => $paymentProviderId,
                    'payment_method_id' => $newPaymentMethod->id,
                    'name' => 'default',
                    'original_id' => $subscription->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'price' => $plan->amount,
                    'quantity' => $subscription->quantity,
                    'trial_start' => $subscription->trial_start,
                    'trial_end' => $subscription->trial_end,
                    'created' => $subscription->created,
                    'expired_at' => $expiry_at,
                ]);

                //order confirmation
                dispatch(new SendOrderMailJob($subscription));
            }
            catch(Exception $exception){
                Log::error("SubModel: ".get_called_class() . ': ' . $exception->getMessage());
                throw new $exception("An error occurred while creating Subscription Model: ". $exception->getMessage());
            }

            try{
                $cusTranaction = CustomerTransaction::insertPayment([
                    'customer_id' => $customerData->id,
                    'subscription_id' => $newSubscription->id,
                    'currency_id' => $currency->id,
                    'payment_provider_id' => $paymentProviderId,
                    'payment_status' => $subscription->status,
                    'amount' => $plan->amount,
                    'transaction_id' => uniqid(),
                    'response' => json_encode($subscription)
                ]);
            }
            catch(Exception $exception){
                Log::error("CustomerTransaction: ".get_called_class() . ': ' . $exception->getMessage());
                throw new $exception("An error occurred while creating Customer Transaction: ". $exception->getMessage());
            }
            DB::commit();

            $data = [
                "subscription"=> $newSubscription,
                "customer_transaction"=> $cusTranaction,
                "currency"=> $subscription? $subscription->currency : 'gbp',
                "plan" => $plan,
            ];

            return $data;
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("DefaultCatch: ".get_called_class() . ': ' . $e->getMessage());
            throw new $e($e->getMessage());
        }
    }
}