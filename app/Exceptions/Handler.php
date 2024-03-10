<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Modules\Core\Traits\RespondsWithJson;
use Throwable;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Handler extends ExceptionHandler
{
    use RespondsWithJson;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedException) {
            return $this->sendError('You do not have permission to access this resource.', [], Response::HTTP_FORBIDDEN);
        }

        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = $exception->getMessage() ?: 'An error occurred.';

        return $this->sendError($message, [], $statusCode);
    }
}
