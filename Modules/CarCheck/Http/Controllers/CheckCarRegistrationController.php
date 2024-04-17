<?php

namespace Modules\CarCheck\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CarCheck\Repositories\Contracts\CarCheckRepositoryInterface;

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
    public function checkRegNumber(string $car_reg_number=null){
        return $this->carCheckRepository->checkCarRegNumber($car_reg_number);
    }
}
