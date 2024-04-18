<?php

namespace Modules\PaymentProvider\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\PaymentProvider\Http\Resources\PlanResource;
use Modules\PaymentProvider\Repositories\Contracts\PlanRepositoryInterface;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class PlanController extends Controller
{
    private $planRepository;
    public function __construct(PlanRepositoryInterface $planRepository) {
        $this->planRepository = $planRepository;
    }

    /**
     * @OA\Get(
     *   path="/api/plans",
     *   tags={"Plans"},
     *   summary="Get list of plans",
     *   description="Returns all plans with optional search, pagination, and sorting.",
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Plan")
     *       ),
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="payment provides fetched successfully"),
     *       @OA\Property(property="links", type="object",
     *         @OA\Property(property="first", type="string", example="http://api.example.com/api/plans?page=1"),
     *         @OA\Property(property="last", type="string", example="http://api.example.com/api/plans?page=10"),
     *         @OA\Property(property="prev", type="string", example="null"),
     *         @OA\Property(property="next", type="string", example="http://api.example.com/api/plans?page=2")
     *       ),
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="current_page", type="integer", example=1),
     *         @OA\Property(property="from", type="integer", example=1),
     *         @OA\Property(property="last_page", type="integer", example=10),
     *         @OA\Property(property="path", type="string", example="http://api.example.com/api/plans"),
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
            $plans = $this->planRepository->all();
            
            $resource = PlanResource::collection($plans);
            return $this->sendSuccess($resource, "Plans fetched successfully", Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::info('An error occurred while fetching plans data: ', $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/plans",
     *     tags={"Plans"},
     *     summary="Create a new Plan",
     *     description="Creates a new Plan with the given details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "symbol"},
     *             @OA\Property(property="name", type="string", example="Plan Name"),
     *             @OA\Property(property="symbol", type="string", example=$)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan Created Successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Plan")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plan not found"
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
                'name' => 'required|unique:plans,name',
                'plan_code' => 'required|unique:plans,name',
                'amount_trial' => 'required|integer',
                'amount_premium' => 'required|integer',
                'provider_plan_id' => 'required|string|min:10',
                'payment_provider_id' => 'required|integer',
                'currency_id' => 'required|integer',
            ]);

            $plan = $this->planRepository->create($data);
            $planResouce = new PlanResource($plan);
            return $this->sendSuccess($planResouce, "Plan created successfully", Response::HTTP_CREATED);
        } catch (Exception $ex) {
            Log::info('Received data: ', $request->all());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/plans/{id}",
     *   tags={"Plans"},
     *   summary="Get a single plan by id",
     *   description="Returns a single plan by id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref="#/components/schemas/Plan")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Plan not found"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function show(int $id): JsonResponse
    {
        try{
            $plan = $this->planRepository->find($id);
            $resource = new PlanResource($plan);
            return $this->sendSuccess($resource, "Plan fetched successfully", Response::HTTP_OK);
        }catch(ModelNotFoundException $ex){
            Log::info('Plan not found: '. $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_NOT_FOUND);
        } 
        catch (Exception $ex) {
            Log::info('An error occurred while fetching plan data: ', $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Put(
     *   path="/api/plans/{id}",
     *   tags={"Plans"},
     *   summary="Update an existing plan",
     *   description="Updates the plan identified by the given id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="id of the plan to update",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Data for updating the plan",
     *     @OA\JsonContent(ref="#/components/schemas/Plan")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Plan updated successfully",
     *     @OA\JsonContent(ref="#/components/schemas/Plan")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Plan not found"
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
                'name' => 'sometimes|unique:plans,name',
                'plan_code' => 'sometimes|unique:plans,name',
                'amount_trial' => 'sometimes|integer',
                'amount_premium' => 'sometimes|integer',
                'provider_plan_id' => 'sometimes|string|min:10',
                'payment_provider_id' => 'sometimes|integer',
                'currency_id' => 'sometimes|integer',
            ]);
            
            $planUpdated = $this->planRepository->update($data, $id);
            $plan = new PlanResource($planUpdated);
            return $this->sendSuccess($plan, "Plan updated successfully", Response::HTTP_CREATED);
        } catch(ModelNotFoundException $ex){
            Log::info('Plan not found: '. $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_NOT_FOUND);
        }  
        catch (Exception $ex) {
            Log::info('Received data: ', $request->all());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/plans/{id}",
     *   tags={"Plans"},
     *   summary="Delete a plan",
     *   description="Deletes a plan identified by id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the plan to be deleted",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Plan deleted successfully",
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
     *         example="Plan deleted successfully"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Plan not found",
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
     *         example="Plan not found"
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
     *         example="An error occurred while deleting the plan"
     *       )
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->planRepository->delete($id);
            return $this->sendSuccess([], "Plan deleted successfully", Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Plan deletion failed', ['error' => 'An error occurred while deleting the plan: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
