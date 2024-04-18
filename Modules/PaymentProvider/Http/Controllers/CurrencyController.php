<?php

namespace Modules\PaymentProvider\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\PaymentProvider\Http\Resources\CurrencyResource;
use Modules\PaymentProvider\Repositories\Contracts\CurrencyRepositoryInterface;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class CurrencyController extends Controller
{
    private $currencyRepository;
    public function __construct(CurrencyRepositoryInterface $currencyRepository) {
        $this->currencyRepository = $currencyRepository;
    }
    
    /**
     * @OA\Get(
     *   path="/api/currencies",
     *   tags={"Currencies"},
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
     *       @OA\Property(property="links", type="object",
     *         @OA\Property(property="first", type="string", example="http://api.example.com/api/currencies?page=1"),
     *         @OA\Property(property="last", type="string", example="http://api.example.com/api/currencies?page=10"),
     *         @OA\Property(property="prev", type="string", example="null"),
     *         @OA\Property(property="next", type="string", example="http://api.example.com/api/currencies?page=2")
     *       ),
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="current_page", type="integer", example=1),
     *         @OA\Property(property="from", type="integer", example=1),
     *         @OA\Property(property="last_page", type="integer", example=10),
     *         @OA\Property(property="path", type="string", example="http://api.example.com/api/currencies"),
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
            $currencies = $this->currencyRepository->all();
            $resource = CurrencyResource::collection($currencies);
            return $this->sendSuccess($resource, "Currencies fetched successfully", Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::info('An error occurred while fetching currencies data: ', $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/currencies",
     *     tags={"Currencies"},
     *     summary="Create a new Currency",
     *     description="Creates a new Currency with the given details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "symbol"},
     *             @OA\Property(property="name", type="string", example="Currency Name"),
     *             @OA\Property(property="symbol", type="string", example=$)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Currency Created Successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Currency")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Currency not found"
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
                'code' => 'required|string|unique:currencies,code',
                'symbol' => 'required|string'
            ]);

            $currency = $this->currencyRepository->create($data);
            $currencyResouce = new CurrencyResource($currency);
            return $this->sendSuccess($currencyResouce, "Currency created successfully", Response::HTTP_CREATED);
        } catch (Exception $ex) {
            Log::info('Received data: ', $request->all());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/currencies/{id}",
     *   tags={"Currencies"},
     *   summary="Get a single currency by id",
     *   description="Returns a single currency by id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref="#/components/schemas/Currency")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Currency not found"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function show(int $id){
        try{
            $currency = $this->currencyRepository->find($id);
            $resource = new CurrencyResource($currency);
            return $this->sendSuccess($resource, "Currency fetched successfully", Response::HTTP_OK);
        }catch(ModelNotFoundException $ex){
            Log::info('Currency not found: '. $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_NOT_FOUND);
        } 
        catch (Exception $ex) {
            Log::info('An error occurred while fetching currency data: ', $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @OA\Put(
     *   path="/api/currencies/{id}",
     *   tags={"Currencies"},
     *   summary="Update an existing currency",
     *   description="Updates the currency identified by the given id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="id of the currency to update",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Data for updating the currency",
     *     @OA\JsonContent(ref="#/components/schemas/Currency")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ccurrency updated successfully",
     *     @OA\JsonContent(ref="#/components/schemas/Currency")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Currency not found"
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
                'code' => 'sometimes|string',
                'symbol' => 'sometimes|string'
            ]);
            
            $currencyUpdated = $this->currencyRepository->update($data, $id);
            $currency = new CurrencyResource($currencyUpdated);
            return $this->sendSuccess($currency, "Currency updated successfully", Response::HTTP_CREATED);
        } catch(ModelNotFoundException $ex){
            Log::info('Currency not found: '. $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_NOT_FOUND);
        }  
        catch (Exception $ex) {
            Log::info('Received data: ', $request->all());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/currencies/{id}",
     *   tags={"Currencies"},
     *   summary="Delete a Currency",
     *   description="Deletes a Currency identified by id.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the Currency to be deleted",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Currency deleted successfully",
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
     *         example="Currency deleted successfully"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Currency not found",
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
     *         example="Currency not found"
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
     *         example="An error occurred while deleting the Currency"
     *       )
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->currencyRepository->delete($id);
            return $this->sendSuccess([], "Currency deleted successfully", Response::HTTP_OK);
        } catch(ModelNotFoundException $ex){
            Log::info('Currency not found: '. $ex->getMessage());
            return $this->sendError($ex->getMessage(), [], Response::HTTP_NOT_FOUND);
        }  
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Currency deletion failed', ['error' => 'An error occurred while deleting the Currency: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
