<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\DTOs\LoginData;
use App\Infrastructure\Models\User;

class AuthService
{
    public function __construct(
        private LoginAction $loginAction,
        private LogoutAction $logoutAction,
    ) {}

    public function login(LoginData $data): array
    {
        return $this->loginAction->execute($data);
    }

    public function logout(User $user, bool $allDevices = false): bool
    {
        return $this->logoutAction->execute($user, $allDevices);
    }

    public function user(): ?User
    {
        return auth()->user();
    }
}
