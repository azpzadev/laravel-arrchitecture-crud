<?php

declare(strict_types=1);

namespace App\Domain\Auth\Events;

use App\Infrastructure\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a user successfully logs in.
 *
 * This event is fired after successful authentication and can be
 * used to trigger actions like logging, notifications, or analytics.
 *
 * @property User $user The authenticated user
 * @property string $deviceName The device/client identifier
 * @property string|null $ipAddress The IP address of the request
 */
class UserLoggedIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new UserLoggedIn event instance.
     *
     * @param User $user The user who logged in
     * @param string $deviceName The device name used for the token
     * @param string|null $ipAddress The IP address of the login request
     */
    public function __construct(
        public User $user,
        public string $deviceName,
        public ?string $ipAddress = null,
    ) {}
}
