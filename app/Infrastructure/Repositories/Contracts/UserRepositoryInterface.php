<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Contracts;

use App\Infrastructure\Models\User;

/**
 * Interface for user repository implementations.
 *
 * Extends base repository with user-specific query methods
 * for authentication and user management operations.
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a user by username.
     *
     * @param string $username The username to search
     * @return User|null The user or null if not found
     */
    public function findByUsername(string $username): ?User;

    /**
     * Find a user by email address.
     *
     * @param string $email The email address to search
     * @return User|null The user or null if not found
     */
    public function findByEmail(string $email): ?User;

    /**
     * Check if a user exists with the given username.
     *
     * @param string $username The username to check
     * @return bool True if a user exists with this username
     */
    public function existsByUsername(string $username): bool;

    /**
     * Check if a user exists with the given email.
     *
     * @param string $email The email address to check
     * @return bool True if a user exists with this email
     */
    public function existsByEmail(string $email): bool;

    /**
     * Find all active users.
     *
     * @return array Array of active User models
     */
    public function getActiveUsers(): array;

    /**
     * Delete all tokens for a user on a specific device.
     *
     * @param User $user The user whose tokens to delete
     * @param string $deviceName The device name to filter tokens
     * @return int Number of deleted tokens
     */
    public function deleteTokensByDevice(User $user, string $deviceName): int;

    /**
     * Delete all tokens for a user.
     *
     * @param User $user The user whose tokens to delete
     * @return int Number of deleted tokens
     */
    public function deleteAllTokens(User $user): int;

    /**
     * Delete the current access token for a user.
     *
     * @param User $user The user whose current token to delete
     * @return bool True if token was deleted
     */
    public function deleteCurrentToken(User $user): bool;

    /**
     * Create a new access token for a user.
     *
     * @param User $user The user to create token for
     * @param string $deviceName The device name for the token
     * @return string The plain text token
     */
    public function createToken(User $user, string $deviceName): string;
}
