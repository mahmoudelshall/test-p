<?php

use Illuminate\Http\JsonResponse;

/**
 * [message return the api responses]
 * @param  boolean $status  [status true success false for failure]
 * @param  array   $data    [object will be sent]
 * @param  array   $message [message will be sent]
 * @param  integer  $code [for response code]
 * @return JsonResponse
 */
if (!function_exists('message')) {
    function message($error = true, $data = null, $message = [], $code = 200): JsonResponse
    {
        return response()->json([
            'status' => [
                'error' => $error,
                'message' => $message,
                'code' => $code,
            ],
            'data' => $data,
        ], $code);
    }
}