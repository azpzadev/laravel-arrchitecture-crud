<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthTokenResource;
use App\Http\Resources\Auth\AuthUserResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for authentication operations.
 *
 * Handles login, logout, and user profile endpoints.
 */
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @param AuthService $authService The authentication service
     */
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Authenticate user and generate access token.
     *
     * @param LoginRequest $request The validated login request
     * @return JsonResponse User data and access token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->toDto());

        return ApiResponse::success([
            'user' => new AuthUserResource($result['user']),
            'token' => new AuthTokenResource($result['token']),
        ], 'Login successful');
    }

    /**
     * Revoke the current access token.
     *
     * @param Request $request The incoming request
     * @return JsonResponse Success message
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ApiResponse::success(message: 'Logged out successfully');
    }

    /**
     * Revoke all access tokens for the user.
     *
     * @param Request $request The incoming request
     * @return JsonResponse Success message
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logout($request->user(), allDevices: true);

        return ApiResponse::success(message: 'Logged out from all devices successfully');
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param Request $request The incoming request
     * @return JsonResponse The user data
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::resource(
            new AuthUserResource($request->user()),
            'User retrieved successfully'
        );
    }
}
