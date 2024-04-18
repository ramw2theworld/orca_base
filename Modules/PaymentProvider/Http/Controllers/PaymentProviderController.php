<?php

namespace Modules\PaymentProvider\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\PaymentProvider\Http\Resources\PaymentProviderResource;
use Modules\PaymentProvider\Repositories\Contracts\PaymentProviderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\PaymentProvider\Models\PaymentProvider;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class PaymentProviderController extends Controller
{
    private $paymentProviderRepository;
    public function __construct(PaymentProviderRepositoryInterface $paymentProviderRepository) {
        $this->paymentProviderRepository = $paymentProviderRepository;
    }
    
    /**
     * @OA\Get(
     *   path="/api/payment-providers",
     *   tags={"PaymentProviders"},
     *   summary="Get list of PaymentProviders",
     *   description="Returns all PaymentProviders with optional search, pagination, and sorting.",
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/PaymentProvider")
     *       ),
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="payment provides fetched successfully"),
     *       @OA\Property(property="links", type="object",
     *         @OA\Property(property="first", type="string", example="http://api.example.com/api/payment-providers?page=1"),
     *         @OA\Property(property="last", type="string", example="http://api.example.com/api/payment-providers?page=10"),
     *         @OA\Property(property="prev", type="string", example="null"),
     *         @OA\Property(property="next", type="string", example="http://api.example.com/api/payment-providers?page=2")
     *       ),
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="current_page", type="integer", example=1),
     *         @OA\Property(property="from", type="integer", example=1),
     *         @OA\Property(property="last_page", type="integer", example=10),
     *         @OA\Property(property="path", type="string", example="http://api.example.com/api/payment-providers"),
     *         @OA\Property(property="per_page", type="integer", example=15),
     *         @OA\Property(property="to", type="integer", example=15),
     *         @OA\Property(property="total", type="integer", example=150)
     *       )
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        try{
            $paymentProviders = $this->paymentProviderRepository->all();
            $resource = PaymentProviderResource::collection($paymentProviders);
            return $this->sendSuccess($resource, "Payment providers fetched successfully", Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::info('An error occurred while fetching providers data: ', $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/payment-providers",
     *     tags={"PaymentProviders"},
     *     summary="Create a new Payment Provider",
     *     description="Creates a new payment provider with the given details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "is_active"},
     *             @OA\Property(property="name", type="string", example="Provider Name"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment Provider Created Successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaymentProvider")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment Provider not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error, please send valid data"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request){
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'is_active' => 'required|string|in:true,false'
            ]);
            $data['is_active'] = $data['is_active'] === 'true';

            $paymentProvider = $this->paymentProviderRepository->create($data);
            $provider = new PaymentProviderResource($paymentProvider);
            return $this->sendSuccess($provider, "Payment provider created successfully", Response::HTTP_CREATED);
        } catch (Exception $ex) {
            Log::info('Received data: ', $request->all());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/payment-providers/{id}",
     *   tags={"Payment Providers"},
     *   summary="Get a single provider by id",
     *   description="Returns a single provider by id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref="#/components/schemas/PaymentProvider")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Payment Provider not found"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function show(int $id){
        try{
            $paymentProvider = $this->paymentProviderRepository->find($id);
            $resource = new PaymentProviderResource($paymentProvider);
            return $this->sendSuccess($resource, "Payment provider fetched successfully", Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::info('An error occurred while fetching providers data: ', $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @OA\Put(
     *   path="/api/payment-providers/{id}",
     *   tags={"Payment Providers"},
     *   summary="Update an existing provider",
     *   description="Updates the provider identified by the given id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="id of the provider to update",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Data for updating the provider",
     *     @OA\JsonContent(ref="#/components/schemas/PaymentProvider")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="PaymentProvider updated successfully",
     *     @OA\JsonContent(ref="#/components/schemas/PaymentProvider")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="PaymentProvider not found"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validate your inputs"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, int $id)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string',
                'is_active' => 'sometimes|string|in:true,false'
            ]);
            if(isset($data['is_active']))
                $data['is_active'] = $data['is_active'] === 'true';

            $paymentProvider = $this->paymentProviderRepository->update($data, $id);
            $provider = new PaymentProviderResource($paymentProvider);
            return $this->sendSuccess($provider, "Payment provider updated successfully", Response::HTTP_CREATED);
        } catch (Exception $ex) {
            Log::info('Received data: ', $request->all());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/payment-providers/{id}",
     *   tags={"Payment Providers"},
     *   summary="Delete a provider",
     *   description="Deletes a provider identified by id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the provider to be deleted",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Provider deleted successfully",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="success",
     *         type="boolean",
     *         example=true
     *       ),
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Provider deleted successfully"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Provider not found",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="success",
     *         type="boolean",
     *         example=false
     *       ),
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Provider not found"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="success",
     *         type="boolean",
     *         example=false
     *       ),
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="An error occurred while deleting the Provider"
     *       )
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->paymentProviderRepository->delete($id);
            return $this->sendSuccess([], "Payment provider deleted successfully", Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Payment provider deletion failed', ['error' => 'An error occurred while deleting the Provider: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
