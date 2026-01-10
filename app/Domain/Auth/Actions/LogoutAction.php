<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Events\UserLoggedOut;
use App\Infrastructure\Models\User;

/**
 * Handles user logout and token revocation.
 *
 * This action revokes either the current access token or all
 * access tokens for the user, and dispatches the UserLoggedOut event.
 */
class LogoutAction
{
    /**
     * Execute the logout process.
     *
     * Revokes the user's access token(s) based on the allDevices flag.
     * When allDevices is true, all tokens are revoked (logout from all devices).
     * When false, only the current token is revoked.
     *
     * @param User $user The user to log out
     * @param bool $allDevices Whether to revoke all tokens (default: false)
     * @return bool True on successful logout
     */
    public function execute(User $user, bool $allDevices = false): bool
    {
        if ($allDevices) {
            $user->tokens()->delete();
        } else {
            $user->currentAccessToken()->delete();
        }

        event(new UserLoggedOut($user, $allDevices));

        return true;
    }
}
