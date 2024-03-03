<?php 
namespace Modules\Core\Traits;

use Illuminate\Http\Response;

trait RespondsWithJson
{
    // Method for success response
    public function sendSuccess($data, $message = null, $code = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    // Method for error response
    public function sendError($message, $errors = [], $code = Response::HTTP_BAD_REQUEST)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}