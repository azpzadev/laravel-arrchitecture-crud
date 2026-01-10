<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Events\UserLoggedOut;
use App\Infrastructure\Models\User;

class LogoutAction
{
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
