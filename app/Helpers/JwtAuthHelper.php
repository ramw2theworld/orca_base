<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthHelper {
    /**
     * Authenticate a user via JWT token and return the user.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function authenticateUser() {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                return null;
            }
            else{
                return JWTAuth::user();
            }
        }
        catch (JWTException $e) {
            return null;
        }

    }
}
