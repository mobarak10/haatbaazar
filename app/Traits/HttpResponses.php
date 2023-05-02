<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HttpResponses
{
    /**
     * when successful an action then call this method
     * @param $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function success($data, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Request was successful',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * when an error is occurs then call this method
     * @param $data
     * @param int $code
     * @param string|null $message
     * @return JsonResponse
     */
    protected function error($data, int $code, ?string $message = null): JsonResponse
    {
        return response()->json([
            'status' => 'An error has occurred',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * when return empty data then call this method
     * @param $data
     * @return JsonResponse
     */
    protected function emptyData($data): JsonResponse
    {
        return response()->json([
            'status' => 'No data found',
            'data' => $data
        ]);
    }
}
