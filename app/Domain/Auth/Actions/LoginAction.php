<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\AuthTokenData;
use App\Domain\Auth\DTOs\LoginData;
use App\Domain\Auth\Events\UserLoggedIn;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Infrastructure\Models\User;
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
        $user = User::where('username', $data->username)->first();

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        // Revoke all previous tokens for this device
        $user->tokens()->where('name', $data->deviceName)->delete();

        // Create new token
        $token = $user->createToken($data->deviceName);

        event(new UserLoggedIn($user, $data->deviceName, request()->ip()));

        return [
            'user' => $user,
            'token' => new AuthTokenData(
                accessToken: $token->plainTextToken,
                tokenType: 'Bearer',
            ),
        ];
    }
}
