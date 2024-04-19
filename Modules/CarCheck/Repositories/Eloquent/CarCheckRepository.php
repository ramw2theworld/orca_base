<?php

namespace Modules\CarCheck\Repositories\Eloquent;

use Modules\CarCheck\Repositories\Contracts\CarCheckRepositoryInterface;
use Modules\CarCheck\Services\FetchDataFromAPI;
use App\Helpers\JwtAuthHelper;
use Tymon\JWTAuth\Facades\JWTAuth;

class CarCheckRepository implements CarCheckRepositoryInterface
{
    private FetchDataFromAPI $fetchDataFetchAPI;

    public function __construct(FetchDataFromAPI $fetch) {
        $this->fetchDataFetchAPI = $fetch;
    }

    public function checkCarRegNumber(string $reg_number){
        // $user = JwtAuthHelper::authenticateUser();
        $user = JWTAuth::user();

        $apis = [
            'paid' => [
                // 'BatteryData',
                // 'FuelPriceData',
                // 'MotHistoryAndTaxStatusData',
                // 'PostcodeLookup',
                // 'SpecAndOptionsData',
                // 'TyreData',
                // 'ValuationCanPrice',
                // 'ValuationData',
                'VdiCheckFull',
                // 'VehicleAndMotHistory',
                // 'VehicleTaxData',
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
    }
}
