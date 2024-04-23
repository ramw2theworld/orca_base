<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\AuthorizationException; 
use Symfony\Component\HttpFoundation\Response;

class JwtTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token && $request->hasCookie('jwt')) {
                $token = $request->cookie('jwt');
                JWTAuth::setToken($token); 
                $user = JWTAuth::authenticate($token);
            } 
            else {
                $user = JWTAuth::parseToken()->authenticate();
            }

            if (!$user) {
                throw new AuthorizationException('User not found');
            }
        } catch (JWTException $e) {
            \Log::info('Authenticated user:', ['user' => $user]);
            \Log::info('User roles:', ['roles' => $user->roles()->pluck('name')]);
            \Log::info('User permissions:', ['permissions' => $user->getAllPermissions()->pluck('name')]);


            $message = 'Unauthorized';
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $message = 'Token expired';
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                $message = 'Token is invalid';
            }
            throw new AuthorizationException($message);
        }

        // $this->authorize($request, $user);

        return $next($request);
    }

    /**
     * Perform authorization checks based on the requested route's required roles/permissions.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @throws \App\Exceptions\AuthorizationException
     */
    protected function authorize(Request $request, $user)
    {
        $route = $request->route();

        if ($roles = $route->middleware('role')) {
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    return;
                }
            }
        }

        if ($permissions = $route->middleware('permission')) {
            foreach ($permissions as $permission) {
                if ($user->hasPermissionTo($permission)) {
                    return;
                }
            }
        }

        throw new AuthorizationException('You do not have permission to access this resource.');
    }
}
