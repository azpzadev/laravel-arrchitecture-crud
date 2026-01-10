<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\AuthTokenData;
use App\Domain\Auth\DTOs\LoginData;
use App\Domain\Auth\Events\UserLoggedIn;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Infrastructure\Models\User;
use App\Infrastructure\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

/**
 * Handles user authentication and token generation.
 *
 * This action validates user credentials, revokes existing tokens
 * for the same device, creates a new access token, and dispatches
 * the UserLoggedIn event.
 */
class LoginAction
{
    /**
     * Create a new LoginAction instance.
     *
     * @param UserRepositoryInterface $userRepository The user repository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * Execute the login process.
     *
     * Validates the provided credentials against the database,
     * revokes any existing tokens for the same device name,
     * and creates a new Sanctum token.
     *
     * @param LoginData $data The login credentials DTO
     * @return array{user: User, token: AuthTokenData} The authenticated user and token
     *
     * @throws InvalidCredentialsException When username or password is incorrect
     */
    public function execute(LoginData $data): array
    {
        $user = $this->userRepository->findByUsername($data->username);

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        // Revoke all previous tokens for this device
        $this->userRepository->deleteTokensByDevice($user, $data->deviceName);

        // Create new token
        $accessToken = $this->userRepository->createToken($user, $data->deviceName);

        event(new UserLoggedIn($user, $data->deviceName, request()->ip()));

        return [
            'user' => $user,
            'token' => new AuthTokenData(
                accessToken: $accessToken,
                tokenType: 'Bearer',
            ),
        ];
    }
}
