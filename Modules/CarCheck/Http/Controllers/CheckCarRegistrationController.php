<?php

namespace Modules\CarCheck\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CarCheck\Repositories\Contracts\CarCheckRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class CheckCarRegistrationController extends Controller
{
    protected $carCheckRepository;
    public function __construct(CarCheckRepositoryInterface $carCheckRepository){
        $this->carCheckRepository = $carCheckRepository;
    }

    /**
     * @OA\Get(
     *   path="/api/v1/car-check/{car_reg_number}",
     *   tags={"Car Check"},
     *   summary="Get car information by Registration Number",
     *   description="Returns information of a car by its registration number.",
     *   @OA\Parameter(
     *     name="car_reg_number",
     *     in="path",
     *     required=true,
     *     description="The registration number of the car to fetch details for.",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="make", type="string", example="XPP 590"),
     *         @OA\Property(property="model", type="string", example="POOW 67R"),
     *         @OA\Property(property="year", type="integer", example=2020)
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Car not found"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function checkRegNumber(string $car_reg_number){
        try{
            $validator = Validator::make(['car_reg_number' => $car_reg_number], [
                'car_reg_number' => 'required|string|max:10|regex:/^[A-Z0-9]+$/i',
            ]);
        
            if ($validator->fails()) {
                return $this->sendError('Please put valid car registration number', $validator->errors(), 422);
            }
            
            $data = $this->carCheckRepository->checkCarRegNumber($car_reg_number);
            return $this->sendSuccess($data, 'Car details fetched successfully', Response::HTTP_OK);
        }catch(Exception $exception){
            $this->sendError('Something went wrong while fetching card details: '.$exception->getMessage(), [], 500);
        }
    }
}
