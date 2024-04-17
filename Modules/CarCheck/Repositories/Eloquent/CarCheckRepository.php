<?php

namespace Modules\CarCheck\Repositories\Eloquent;

use Exception;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Modules\CarCheck\Repositories\Contracts\CarCheckRepositoryInterface;
use Modules\CarCheck\Services\FetchDataFromAPI;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Helpers\JwtAuthHelper;
use Tymon\JWTAuth\JWT;

class CarCheckRepository implements CarCheckRepositoryInterface
{
    private FetchDataFromAPI $fetchDataFetchAPI;

    public function __construct(FetchDataFromAPI $fetch) {
        $this->fetchDataFetchAPI = $fetch;
    }

    public function checkCarRegNumber(string $reg_number){
        $user = JwtAuthHelper::authenticateUser();
        $apis = [
            'paid' => [
                'BatteryData',
                'FuelPriceData',
                'MotHistoryAndTaxStatusData',
                'PostcodeLookup',
                'SpecAndOptionsData',
                'TyreData',
                'ValuationCanPrice',
                'ValuationData',
                'VdiCheckFull',
                'VehicleAndMotHistory',
                'VehicleTaxData',
            ],
            'unpaid' => [
                'MotHistoryData',
                'VehicleData',
                'VehicleDataIRL',
                'VehicleImageData',
            ]
        ];
        //check log in
        if($user){
            if($user->hasSubscription()){
                $infos = $this->fetchDataFetchAPI->init($apis['paid'], $reg_number);
            }
            else{
                $infos = $this->fetchDataFetchAPI->init($apis['unpaid'], $reg_number);
            }

            return $infos;
        }
        else{
            return $this->fetchDataFetchAPI->init($apis['unpaid'], $reg_number);

        }
        
        

        // try {
        //     if (JWTAuth::parseToken()->authenticate()) {
        //         $infos = $this->fetchDataFetchAPI->init($paid, $reg_number);
        //     } else {
        //         $infos = $this->fetchDataFetchAPI->init($unPaid, $reg_number);
        //     }
        //     return $infos;
        // } catch (TokenExpiredException $e) {
        //     Log::warning("Expired token: " . $e->getMessage());
        //     $infos = $this->fetchDataFetchAPI->init($unPaid, $reg_number);
        //     return $infos;
        // } catch (TokenInvalidException $e) {
        //     Log::warning("Invalid token: " . $e->getMessage());
        //     $infos = $this->fetchDataFetchAPI->init($unPaid, $reg_number);
        //     return $infos;
        // } catch (JWTException $e) {
        //     Log::error("JWT error: " . $e->getMessage());
        //     $infos = $this->fetchDataFetchAPI->init($unPaid, $reg_number);
        //     return $infos;
        // } catch (Exception $e) {
        //     Log::error("Unexpected error: " . $e->getMessage());
        //     return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        // }
    }
}
