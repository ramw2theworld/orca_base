<?php
namespace Modules\Core\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait RespondsWithJson
{
    /**
     * Method for success response
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSuccess($data, $message = null, $code = Response::HTTP_OK, $headers = []): JsonResponse
    {
        // Check if data is an instance of an API Resource or Resource Collection
        if ($data instanceof \Illuminate\Http\Resources\Json\JsonResource || $data instanceof \Illuminate\Http\Resources\Json\ResourceCollection) {
            // Get the response from the resource
            $response = $data->response()->setStatusCode($code);

            // Add headers if provided
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }

            // If a message is provided, add it to the response's JSON
            if ($message !== null) {
                $response->setContent(json_encode(array_merge([
                    'success' => true,
                    'message' => $message,
                ], json_decode($response->getContent(), true))));
            }

            return $response;
        } else {
            // Handle normal data arrays or other content
            $response = response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $code);

            // Add headers if provided
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }

            return $response;
        }
    }

    // Method for error response (unchanged)
    public function sendError($message, $errors = [], $code = Response::HTTP_BAD_REQUEST): JsonResponse
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
