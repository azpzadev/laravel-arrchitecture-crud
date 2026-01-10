<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\DTOs\AuthTokenData;
use App\Domain\Auth\DTOs\LoginData;
use App\Infrastructure\Models\User;
use App\Infrastructure\Repositories\Contracts\UserRepositoryInterface;

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
     * @param UserRepositoryInterface $userRepository The user repository
     * @param LoginAction $loginAction The login action handler
     * @param LogoutAction $logoutAction The logout action handler
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoginAction $loginAction,
        private LogoutAction $logoutAction,
    ) {}

    /**
     * Authenticate a user with credentials.
     *
     * @param LoginData $data The login credentials
     * @return array{user: User, token: AuthTokenData}
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

    /**
     * Find a user by UUID.
     *
     * @param string $uuid The user UUID
     * @return User|null The user or null if not found
     */
    public function findByUuid(string $uuid): ?User
    {
        /** @var User|null */
        return $this->userRepository->findByUuid($uuid);
    }

    /**
     * Find a user by username.
     *
     * @param string $username The username
     * @return User|null The user or null if not found
     */
    public function findByUsername(string $username): ?User
    {
        return $this->userRepository->findByUsername($username);
    }

    /**
     * Find a user by email.
     *
     * @param string $email The email address
     * @return User|null The user or null if not found
     */
    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }
}
