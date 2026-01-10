<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\DTOs\LoginData;
use App\Infrastructure\Models\User;

/**
 * Service layer for authentication operations.
 *
 * Orchestrates authentication actions including login, logout,
 * and user retrieval. Acts as the main entry point for auth
 * operations from controllers.
 */
class AuthService
{
    /**
     * Create a new AuthService instance.
     *
     * @param LoginAction $loginAction The login action handler
     * @param LogoutAction $logoutAction The logout action handler
     */
    public function __construct(
        private LoginAction $loginAction,
        private LogoutAction $logoutAction,
    ) {}

    /**
     * Authenticate a user with credentials.
     *
     * @param LoginData $data The login credentials
     * @return array{user: User, token: \App\Domain\Auth\DTOs\AuthTokenData}
     *
     * @throws \App\Domain\Auth\Exceptions\InvalidCredentialsException
     */
    public function login(LoginData $data): array
    {
        return $this->loginAction->execute($data);
    }

    /**
     * Log out a user.
     *
     * @param User $user The user to log out
     * @param bool $allDevices Whether to log out from all devices
     * @return bool True on success
     */
    public function logout(User $user, bool $allDevices = false): bool
    {
        return $this->logoutAction->execute($user, $allDevices);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return User|null The authenticated user or null
     */
    public function user(): ?User
    {
        return auth()->user();
    }
}
