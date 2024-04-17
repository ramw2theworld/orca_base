<?php

namespace Modules\CarCheck\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class FetchDataFromAPI {

    protected $client;
    protected $baseUrl = "https://uk1.ukvehicledata.co.uk/api/datapackage";
    protected $apiKey;
    protected $params = [];

    public function __construct() {
        $this->client = new Client();
        $this->apiKey = env('CAR_API_KEY'); 
    }

    public function init(array $apis, string $reg_number){
        $full_data = new Collection();
    
        try{
            foreach ($apis as $api) {
                Log::info("api: ".$api.'\n \n ');
                $endpoint = $this->baseUrl . '/' . $api;
                $query = [
                    'v' => '2',
                    'api_nullitems' => '1',
                    'auth_apikey' => $this->apiKey,
                    'key_VRM' => $reg_number
                ];
    
                if ($api === "FuelPriceData" || $api === "PostcodeLookup") {
                    $query['key_POSTCODE'] = $reg_number;
                }
    
                $response = $this->client->request('GET', $endpoint, ['query' => $query]);
                $data = json_decode($response->getBody()->getContents(), true);
    
                if (!empty($data['Response']['DataItems'])) {
                    $full_data->push($data['Response']['DataItems']);
                } else {
                    $full_data->push([$api => "No Data Available"]);
                }
            }
            return $full_data->toArray();
        }
        catch(Exception $exception){
            Log::error("API request failed: " . $exception->getMessage());
            return ['error' => 'API request failed', 'details' => $exception->getMessage()];
        }
    }
    
    public function fetchCarBatteryData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/FuelPriceData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_POSTCODE=BS12AN');
            $data = $response->getBody()->getContents();

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function fetchFuelPriceData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/FuelPriceData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_POSTCODE=RF53AEE');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::info("hello: ".$e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    public function MotHistoryAndTaxStatusData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/MotHistoryAndTaxStatusData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function MotHistoryData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/MotHistoryData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function fetPostcodeLookup(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/PostcodeLookup?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_POSTCODE=RF53AEE');
            $data = $response->getBody()->getContents();

            return json_decode($data, true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function SpecAndOptionsData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/SpecAndOptionsData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK
            ');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function TyreData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/TyreData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function ValuationCanPrice(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/ValuationCanPrice?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK
            ');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function ValuationData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/ValuationData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function VdiCheckFull(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/VdiCheckFull?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function VehicleAndMotHistory(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/VehicleAndMotHistory?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function VehicleData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/VehicleData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function VehicleDataIRL(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/VehicleDataIRL?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function VehicleImageDataCreated(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/VehicleImageData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }
    public function VehicleTaxData(){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://uk1.ukvehicledata.co.uk/api/datapackage/VehicleTaxData?v=2&api_nullitems=1&auth_apikey=e850138f-d619-401f-9015-5fed87fa3206&key_VRM=KM12AKK');
            $data = $response->getBody()->getContents();
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }

}