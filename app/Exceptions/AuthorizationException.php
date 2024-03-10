<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class AuthorizationException extends Exception
{
    public function render($request)
    {
        // Check if the request expects a JSON response
        if ($request->expectsJson()) {
            return response()->json(['message' => 'You are not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        // For non-API requests, you might redirect to a login page or return a different type of response
        return redirect()->guest(route('login'));
    }
}
