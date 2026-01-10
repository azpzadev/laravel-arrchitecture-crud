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

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->toDto());

        return ApiResponse::success([
            'user' => new AuthUserResource($result['user']),
            'token' => new AuthTokenResource($result['token']),
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ApiResponse::success(message: 'Logged out successfully');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logout($request->user(), allDevices: true);

        return ApiResponse::success(message: 'Logged out from all devices successfully');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::resource(
            new AuthUserResource($request->user()),
            'User retrieved successfully'
        );
    }
}
