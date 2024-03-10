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
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->unauthorized('User not found');
            }

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                throw new AuthorizationException('User not found');
            }
        } catch (JWTException $e) {
            $message = 'Unauthorized';

            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $message = 'Token expired';
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                $message = 'Token is invalid';
            }

            throw new AuthorizationException($message);
        }

        // Perform authorization check based on the requested route's required roles/permissions
        $this->authorize($request, $user);

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

        // Check for role requirement
        if ($roles = $route->middleware('role')) {
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    return; // User has the required role; allow access
                }
            }
        }

        // Check for permission requirement
        if ($permissions = $route->middleware('permission')) {
            foreach ($permissions as $permission) {
                if ($user->hasPermissionTo($permission)) {
                    return; // User has the required permission; allow access
                }
            }
        }

        // If the user does not have the required role or permission, throw an exception
        throw new AuthorizationException('You do not have permission to access this resource.');
    }
}
