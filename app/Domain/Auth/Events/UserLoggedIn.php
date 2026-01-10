<?php

declare(strict_types=1);

namespace App\Domain\Auth\Events;

use App\Infrastructure\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public string $deviceName,
        public ?string $ipAddress = null,
    ) {}
}
