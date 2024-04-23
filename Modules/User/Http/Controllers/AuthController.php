<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
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
    public function login(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError("Credentials do not match", $validator->errors(), Response::HTTP_BAD_REQUEST);
            }

            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return $this->sendError("Invalid credentials", [], Response::HTTP_UNAUTHORIZED);
            }

            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return $this->sendError('Login failed', ['error' => 'Could not generate access token'], 401);
            }

            return $this->respondWithToken($token);
        }
        catch(Exception $exception){
            return $this->sendError('User login failed',
            ['error' => 'An error occurred while generating login: '.$exception->getMessage()],
            Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    protected function respondWithToken(string $token): JsonResponse
    {
        $token_ttl = auth('api')->factory()->getTTL();
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');
        $refreshToken = auth('api')->refresh();
        $cookie = cookie('jwt', $token, 60*24);

        return $this->sendSuccess(
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $token_ttl * 60,
                'user' => [
                    'email' => $user->email,
                    'roles' => $roles,
                    'permissions' => $permissions,
                ],
                'refresh_token'=> $refreshToken
            ], 
            'Access token generated successfully', 200)->withCookie($cookie);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Users"},
     *     summary="Logout a user",
     *     description="Invalidates the user's JWT token to log them out.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized if token is invalid or not provided",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error if there's a problem logging the user out"
     *     )
     * )
     */
    protected function logout(Request $request): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token && $request->hasCookie('jwt')) {
                $token = $request->cookie('jwt');
                JWTAuth::setToken($token);
            }
    
            if (!$token) {
                throw new Exception('No token provided');
            }
    
            JWTAuth::invalidate($token);
            $cookie = Cookie::forget('jwt');
            return $this->sendSuccess([], 'Logout successfully', Response::HTTP_OK)->withCookie($cookie);
        } catch (Exception $exception) {
            return $this->sendError('Logout failed', ['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}