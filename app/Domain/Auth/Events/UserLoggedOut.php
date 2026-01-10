<?php

declare(strict_types=1);

namespace App\Domain\Auth\Events;

use App\Infrastructure\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a user logs out.
 *
 * This event is fired after a user's session/token is revoked
 * and can be used for cleanup, logging, or notifications.
 *
 * @property User $user The user who logged out
 * @property bool $allDevices Whether logout was from all devices
 */
class UserLoggedOut
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new UserLoggedOut event instance.
     *
     * @param User $user The user who logged out
     * @param bool $allDevices True if logged out from all devices
     */
    public function __construct(
        public User $user,
        public bool $allDevices = false,
    ) {}
}
