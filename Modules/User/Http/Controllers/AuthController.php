<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Users"},
     *     summary="make login",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email address",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="user's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Response(
     *     response=200,
     *     description="Logged in Successfully",
     *     @OA\JsonContent(ref="#/components/schemas/User")
     *   ),
    *   @OA\Response(
    *     response=422,
    *     description="Please send valid data"
    *   ),
    * )
    */
    public function login(Request $request)
    {
        $credentials = $request->only("email", "password");
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        $token_ttl = auth('api')->factory()->getTTL();
        return $this->sendSuccess(['data'=>[
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $token_ttl * 60
        ]], 'Access token generated successfully', 200);
    }
}
