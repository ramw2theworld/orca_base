<?php

return [
    // Default configuration values
    "car_api_key" => env("CAR_API_KEY"),
    "battery_url" => "https://uk1.ukvehicledata.co.uk/api/datapackage/BatteryData",
    "battery_params" => "?v=2&api_nullitems=1&auth_apikey=".env("CAR_API_KEY")."&key_VRM=KM12AKK"
];
