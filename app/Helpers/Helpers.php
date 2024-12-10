<?php

use Illuminate\Http\JsonResponse;


if (!function_exists('sendError')) {

    function sendError(string $message, array $errors = [], int $code = 401)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => !empty($errors) ? $errors : null,
        ];

        return response()->json($response, $code);
    }

}



if (!function_exists('sendResponse')) {

    /**
     * Send a response with a standardized structure.
     *
     * @param string $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    function sendResponse(string $message, array $data = [], int $code = 200): JsonResponse
    {

        $response = [
            'success' => true,
            'message' => $message,
            'data' => !empty($data) ? $data : null,
        ];

        // Return the response
        return response()->json($response, $code);
    }
}


