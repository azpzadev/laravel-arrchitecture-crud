<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Standardized API response builder.
 *
 * Provides consistent JSON response formatting for all API endpoints
 * including success, error, and paginated responses.
 */
class ApiResponse
{
    /**
     * Create a success response.
     *
     * @param mixed $data The response data
     * @param string $message Success message
     * @param int $code HTTP status code
     * @return JsonResponse The JSON response
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Create a created (201) response.
     *
     * @param mixed $data The created resource data
     * @param string $message Success message
     * @return JsonResponse The JSON response
     */
    public static function created(
        mixed $data = null,
        string $message = 'Created successfully'
    ): JsonResponse {
        return self::success($data, $message, 201);
    }

    /**
     * Create a no content (204) response.
     *
     * @return JsonResponse The JSON response
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Create an error response.
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param array|null $errors Validation or field-specific errors
     * @param string|null $errorCode Application-specific error code
     * @return JsonResponse The JSON response
     */
    public static function error(
        string $message = 'Error',
        int $code = 400,
        ?array $errors = null,
        ?string $errorCode = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errorCode) {
            $response['error_code'] = $errorCode;
        }

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Create a not found (404) response.
     *
     * @param string $message Error message
     * @return JsonResponse The JSON response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404, errorCode: 'NOT_FOUND');
    }

    /**
     * Create an unauthorized (401) response.
     *
     * @param string $message Error message
     * @return JsonResponse The JSON response
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, 401, errorCode: 'UNAUTHORIZED');
    }

    /**
     * Create a forbidden (403) response.
     *
     * @param string $message Error message
     * @return JsonResponse The JSON response
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, 403, errorCode: 'FORBIDDEN');
    }

    /**
     * Create a validation error (422) response.
     *
     * @param array $errors Field-specific validation errors
     * @param string $message Error message
     * @return JsonResponse The JSON response
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, 422, $errors, 'VALIDATION_ERROR');
    }

    /**
     * Create a server error (500) response.
     *
     * @param string $message Error message
     * @return JsonResponse The JSON response
     */
    public static function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return self::error($message, 500, errorCode: 'SERVER_ERROR');
    }

    /**
     * Create a paginated response with metadata and links.
     *
     * @param LengthAwarePaginator $paginator The paginated data
     * @param string $resourceClass The resource class to transform items
     * @param string $message Success message
     * @return JsonResponse The JSON response with pagination
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        string $resourceClass,
        string $message = 'Success'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resourceClass::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Create a response from a single resource.
     *
     * @param JsonResource $resource The resource to return
     * @param string $message Success message
     * @param int $code HTTP status code
     * @return JsonResponse The JSON response
     */
    public static function resource(
        JsonResource $resource,
        string $message = 'Success',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource,
        ], $code);
    }

    /**
     * Create a response from a resource collection.
     *
     * @param ResourceCollection $collection The collection to return
     * @param string $message Success message
     * @return JsonResponse The JSON response
     */
    public static function collection(
        ResourceCollection $collection,
        string $message = 'Success'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $collection,
        ]);
    }
}
