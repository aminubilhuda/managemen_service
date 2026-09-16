<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a standardized success JSON response.
     */
    protected function successResponse(mixed $data = null, string $message = 'Berhasil', int $code = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a standardized error JSON response.
     */
    protected function errorResponse(string $message = 'Terjadi kesalahan', int $code = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (! is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a standardized paginated JSON response with optional API Resource transformation.
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Berhasil memuat data', ?string $resourceClass = null): JsonResponse
    {
        $data = $paginator->items();

        if ($resourceClass && class_exists($resourceClass)) {
            $data = $resourceClass::collection($data);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ], 200);
    }
}
