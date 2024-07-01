<?php

namespace Modules\PaymentProvider\Http\Controllers;

use App\Helpers\CheckActiveProvider;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\PaymentProvider\Repositories\Contracts\PaymentRepositoryInterface;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class PaymentController extends Controller
{
    protected $paymentRepository;
    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }
    /**
     * @OA\Get(
     *   path="/api/check-active-provider",
     *   tags={"CheckActiveProvider"},
     *   summary="Get list of Currencies",
     *   description="Returns all Currencies with optional search, pagination, and sorting.",
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Currency")
     *       ),
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="payment provides fetched successfully"),
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function checkActiveProvider(){
        $activeProvider = CheckActiveProvider::checkActiveProvider();
        try{
            return $this->sendSuccess($activeProvider, "Active Provider fetched successfully", Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::info('An error occurred while fetching currencies data: ', $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $requestData = $request->all();
            $requestData['email'] = 'ram@window2theworld.org';
            $paymentIntent = $this->paymentRepository->createPaymentIntent($requestData);

            return $this->sendSuccess($paymentIntent, 'Payment intent created successfully', Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error("Error while creating intent: ". $e->getMessage());
            return $this->sendError("Something went wrong: ".$e->getMessage(), [], Response::HTTP_FOUND);
        }
    }

    public function createSubscription(Request $request){
        $request->validate([
            'email' => 'required|email',
        ]);

        $validatedRequest = $request->all();
        $validatedRequest['request_user'] = $request->user();
        $validatedRequest['email'] = $request->email;
        return dd(Auth::user());
        return $validatedRequest;
        try{
            dd(3424323);
            $response = $this->paymentRepository->createSubscription($validatedRequest);
            return $response;
            $data = [
                'hasSubscription' => subscriptionStatus(),
                'subscription'=> $response['subscription'],
                'gmtData' => [
                    'price' => $response["subscription"]->price,
                    'transaction_id' => $response["customer_transaction"]->transaction_id,
                    'currency' => $response["currency"],
                    'email' => auth()->user()->email,
                ]
            ];
            return $this->sendSuccess($data, 'Subscription created successfully', Response::HTTP_CREATED);
        }
        catch (\Exception $exception){
            Log::error("Error while creating subscription: ". $exception->getMessage().' and Line: '.$exception->getLine());
            return $this->sendError("Something went wrong: ". $exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
