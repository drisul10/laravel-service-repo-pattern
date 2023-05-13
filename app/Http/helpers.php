<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

if (!function_exists('apiResponse')) {
  function apiResponse(int $httpCode, string $status, ?string $message = null, $data = null): JsonResponse
  {
    $response = new ApiResponse($httpCode, $status, $message, $data);

    return response()->json($response, $httpCode);
  }
}
