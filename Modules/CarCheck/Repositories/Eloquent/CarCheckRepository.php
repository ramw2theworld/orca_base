<?php

namespace Modules\CarCheck\Repositories\Eloquent;

use Modules\CarCheck\Repositories\Contracts\CarCheckRepositoryInterface;
use Modules\CarCheck\Services\FetchDataFromAPI;
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

        // GX16MKN
        // Stolen
        //  XAR888J
        //  SA15GLZ
        //  F550MAR
        //  F355CHA
        //  F812SFA

        // Finance:
        // V1AFA
        // MF12NKA
        // AC03ELL
        // AM05LLL
        // AU15WLU

        // Write-off
        // R55PAP
        // AD59OER
        // YK04RFA
        // WA66SUH
        // EJ59YOA

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
                //unpaid
                'MotHistoryData',
                'VehicleData',
                'VehicleDataIRL',
                'VehicleImageData',

            ],
            'unpaid' => [
                'VehicleImageData',
                'MotHistoryData',
                'VehicleData',
                // 'VehicleDataIRL',
            ]
        ];
        //check log in
        if($user){
            if($user->hasSubscription()){
                $infos = $this->fetchDataFetchAPI->init($apis['unpaid'], $reg_number);
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
